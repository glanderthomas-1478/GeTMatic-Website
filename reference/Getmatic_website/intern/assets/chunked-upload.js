(function () {
  'use strict';

  // Muss zum PHP-Wert CHUNK_SIZE_BYTES in includes/functions.php passen.
  var CHUNK_SIZE = 8 * 1024 * 1024;
  var MAX_RETRIES_PER_CHUNK = 3;
  var RETRY_DELAY_MS = 1500;

  function randomToken() {
    var bytes = new Uint8Array(24);
    window.crypto.getRandomValues(bytes);
    var hex = '';
    for (var i = 0; i < bytes.length; i++) {
      hex += bytes[i].toString(16).padStart(2, '0');
    }
    return hex;
  }

  function sleep(ms) {
    return new Promise(function (resolve) { setTimeout(resolve, ms); });
  }

  function postForm(url, formData) {
    return fetch(url, { method: 'POST', body: formData, credentials: 'same-origin' })
      .then(function (res) {
        return res.text().then(function (rawText) {
          var data;
          try {
            data = JSON.parse(rawText);
          } catch (e) {
            // Server hat kein gueltiges JSON geliefert (z.B. PHP-Fehlerausgabe).
            // Rohtext als Fehlermeldung durchreichen, damit er sichtbar wird,
            // statt nur "Unexpected end of JSON input" zu zeigen.
            data = { ok: false, error: 'Unerwartete Server-Antwort: ' + rawText.slice(0, 300) };
          }
          return { httpOk: res.ok, data: data };
        });
      });
  }

  function uploadChunk(csrfToken, token, chunkIndex, blob, expectedOffset, meta) {
    var formData = new FormData();
    formData.append('csrf_token', csrfToken);
    formData.append('token', token);
    formData.append('chunk_index', String(chunkIndex));
    formData.append('expected_offset', String(expectedOffset));
    formData.append('chunk', blob, 'chunk');

    if (chunkIndex === 0) {
      formData.append('filename', meta.filename);
      formData.append('total_size', String(meta.totalSize));
    }

    return postForm('upload_chunk.php', formData);
  }

  function uploadChunkWithRetry(csrfToken, token, chunkIndex, blob, expectedOffset, meta) {
    var attempt = 0;

    function tryOnce() {
      attempt++;
      return uploadChunk(csrfToken, token, chunkIndex, blob, expectedOffset, meta)
        .then(function (result) {
          if (!result.httpOk || !result.data || result.data.ok !== true) {
            var message = (result.data && result.data.error) || 'Unbekannter Fehler beim Hochladen.';
            throw new Error(message);
          }
          return result.data;
        })
        .catch(function (err) {
          if (attempt >= MAX_RETRIES_PER_CHUNK) {
            throw err;
          }
          return sleep(RETRY_DELAY_MS).then(tryOnce);
        });
    }

    return tryOnce();
  }

  function finalizeUpload(csrfToken, token) {
    var formData = new FormData();
    formData.append('csrf_token', csrfToken);
    formData.append('token', token);
    return postForm('upload_finalize.php', formData);
  }

  function uploadFile(file, ui) {
    var token = randomToken();
    var totalChunks = Math.ceil(file.size / CHUNK_SIZE);
    var offset = 0;
    var chunkIndex = 0;

    ui.setStatus('Lade hoch: ' + file.name + ' (' + totalChunks + ' Teile) …');
    ui.setProgress(0);

    function nextChunk() {
      if (offset >= file.size) {
        ui.setStatus('Schließe Upload ab …');
        return finalizeUpload(ui.csrfToken, token).then(function (result) {
          if (!result.httpOk || !result.data || result.data.ok !== true) {
            var message = (result.data && result.data.error) || 'Fehler beim Abschließen des Uploads.';
            throw new Error(message);
          }
          return result.data;
        });
      }

      var end = Math.min(offset + CHUNK_SIZE, file.size);
      var blob = file.slice(offset, end);
      var meta = { filename: file.name, totalSize: file.size };

      return uploadChunkWithRetry(ui.csrfToken, token, chunkIndex, blob, offset, meta)
        .then(function (data) {
          offset = data.received_bytes;
          chunkIndex++;
          ui.setProgress(offset / file.size);
          return nextChunk();
        });
    }

    return nextChunk();
  }

  function initUploadForm() {
    var form = document.getElementById('upload-form');
    if (!form) return;

    var fileInput = document.getElementById('upload-file-input');
    var progressWrap = document.getElementById('upload-progress-wrap');
    var progressBar = document.getElementById('upload-progress-bar');
    var statusEl = document.getElementById('upload-status');
    var submitBtn = document.getElementById('upload-submit-btn');
    var csrfToken = form.querySelector('input[name="csrf_token"]').value;

    var ui = {
      csrfToken: csrfToken,
      setProgress: function (fraction) {
        var pct = Math.round(fraction * 100);
        progressBar.style.width = pct + '%';
        progressBar.textContent = pct + '%';
      },
      setStatus: function (text) {
        statusEl.textContent = text;
      },
    };

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      if (!fileInput.files || fileInput.files.length === 0) {
        ui.setStatus('Bitte zuerst eine Datei auswählen.');
        return;
      }

      var file = fileInput.files[0];
      submitBtn.disabled = true;
      progressWrap.classList.add('is-active');
      ui.setProgress(0);

      uploadFile(file, ui)
        .then(function () {
          ui.setStatus('Fertig — „' + file.name + '“ wurde hochgeladen.');
          window.location.reload();
        })
        .catch(function (err) {
          ui.setStatus('Fehler: ' + err.message);
          submitBtn.disabled = false;
        });
    });
  }

  document.addEventListener('DOMContentLoaded', initUploadForm);
})();
