import * as THREE from 'three';
import { OrbitControls } from './vendor/three/OrbitControls.js';

var mount = document.getElementById('docu-3d-canvas');
if (mount) {
  var scene = new THREE.Scene();
  scene.background = null;

  // Visuelles Zentrum der gesamten Baugruppe (Screen + Recheneinheit + Standfuß) —
  // Screen ist bei x=0 symmetrisch, Standfuß/Case sitzen etwas höher als der Boden.
  var focus = new THREE.Vector3(0, 3.1, 0.2);
  var camOffset = new THREE.Vector3(8.6, 3.0, 12.2);

  var camera = new THREE.PerspectiveCamera(32, mount.clientWidth / mount.clientHeight, 0.1, 100);
  camera.position.copy(focus).add(camOffset);

  var renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.setSize(mount.clientWidth, mount.clientHeight);
  renderer.outputColorSpace = THREE.SRGBColorSpace;
  mount.appendChild(renderer.domElement);

  // -----------------------------------------
  // Beleuchtung (3-Punkt-Studio-Setup)
  // -----------------------------------------
  scene.add(new THREE.AmbientLight(0xffffff, 0.85));

  var key = new THREE.DirectionalLight(0xffffff, 1.9);
  key.position.set(8, 10, 6);
  scene.add(key);

  var fill = new THREE.DirectionalLight(0x9fd8d8, 0.85);
  fill.position.set(-8, 4, -4);
  scene.add(fill);

  var rim = new THREE.DirectionalLight(0x5ad7d7, 0.55);
  rim.position.set(0, 6, -10);
  scene.add(rim);

  // -----------------------------------------
  // Materialien
  // -----------------------------------------
  var bodyMat = new THREE.MeshPhysicalMaterial({
    color: 0x33363b,
    roughness: 0.42,
    metalness: 0.08,
    clearcoat: 0.5,
    clearcoatRoughness: 0.3
  });

  var screenMat = new THREE.MeshPhysicalMaterial({
    color: 0x0a0c0f,
    roughness: 0.1,
    metalness: 0.0,
    clearcoat: 1.0,
    clearcoatRoughness: 0.08
  });

  var standMat = new THREE.MeshStandardMaterial({
    color: 0x2c2e32,
    roughness: 0.5,
    metalness: 0.3
  });

  // Label-Texturen (getmatic-Logo vorne + hinten)
  function loadLogo() {
    return new Promise(function (resolve, reject) {
      var img = new Image();
      img.onload = function () { resolve(img); };
      img.onerror = reject;
      img.src = 'getmatic_logo_transparent.png';
    });
  }

  // -----------------------------------------
  // Bildschirm-Gehäuse — nach Referenzfoto (Seitenansicht): schlankes Panel
  // (Bezel + Display) mit einem dickeren Elektronik-Deck unten hinten angesetzt,
  // dadurch der charakteristische Keil-Querschnitt von vorne dünn zu hinten dick.
  // -----------------------------------------
  var device = new THREE.Group();

  var screenW = 8.6, screenH = 5.6;
  var slabD = 0.42;   // Gehäusetiefe des schlanken Panels (Bezel + Display)
  var deckD = 0.78;   // zusätzliche Tiefe des unteren Elektronik-Decks (Gesamttiefe = slabD + deckD)
  var deckH = 1.55;   // Höhe des unteren, dickeren Decks

  // Hauptpanel — dünner Quader, Front zeigt in +Z
  var slabGeo = new THREE.BoxGeometry(screenW, screenH, slabD);
  var slabMesh = new THREE.Mesh(slabGeo, bodyMat);
  device.add(slabMesh);

  // Elektronik-Deck — sitzt unten hinten an, macht das Gehäuse dort deutlich dicker
  var deckGeo = new THREE.BoxGeometry(screenW * 0.94, deckH, deckD);
  var deckMesh = new THREE.Mesh(deckGeo, bodyMat);
  deckMesh.position.set(0, -screenH / 2 + deckH / 2, -slabD / 2 - deckD / 2);
  device.add(deckMesh);

  // Display-Fläche — eigenes Panel knapp vor der Gehäusevorderseite eingelassen
  var faceInset = 0.35;
  var faceGeo = new THREE.PlaneGeometry(screenW - faceInset * 2, screenH - faceInset * 2 - deckH * 0.35);
  var faceMesh = new THREE.Mesh(faceGeo, screenMat);
  faceMesh.position.set(0, deckH * 0.18, slabD / 2 + 0.005);
  device.add(faceMesh);

  // Rückseite: Lüftungsrippen-Textur (feine horizontale Rillen wie im Referenzfoto)
  var backTexCanvas = document.createElement('canvas');
  backTexCanvas.width = 512; backTexCanvas.height = 512;
  (function () {
    var ctx = backTexCanvas.getContext('2d');
    ctx.fillStyle = '#35383d';
    ctx.fillRect(0, 0, 512, 512);
    ctx.strokeStyle = 'rgba(0,0,0,0.4)';
    ctx.lineWidth = 2;
    for (var y = 10; y < 512; y += 6) {
      ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(512, y); ctx.stroke();
    }
  })();
  var ribTex = new THREE.CanvasTexture(backTexCanvas);
  var ribMat = new THREE.MeshStandardMaterial({ map: ribTex, roughness: 0.7, metalness: 0.1 });
  var backPlateGeo = new THREE.PlaneGeometry(screenW - 0.1, screenH - deckH - 0.1);
  var backPlate = new THREE.Mesh(backPlateGeo, ribMat);
  backPlate.rotation.y = Math.PI;
  backPlate.position.set(0, screenH / 2 - (screenH - deckH) / 2 - 0.05, -slabD / 2 - 0.005);
  device.add(backPlate);

  var deckBackPlate = new THREE.Mesh(new THREE.PlaneGeometry(screenW * 0.9, deckH - 0.1), ribMat);
  deckBackPlate.rotation.y = Math.PI;
  deckBackPlate.position.set(0, deckMesh.position.y, -slabD / 2 - deckD - 0.005);
  device.add(deckBackPlate);

  loadLogo().then(function (logo) {
    var logoW = screenW * 0.34, logoH = logoW * (logo.height / logo.width);
    var logoTex = new THREE.Texture(logo);
    logoTex.colorSpace = THREE.SRGBColorSpace;
    logoTex.needsUpdate = true;
    var logoMesh = new THREE.Mesh(
      new THREE.PlaneGeometry(logoW, logoH),
      new THREE.MeshBasicMaterial({ map: logoTex, transparent: true, depthWrite: false })
    );
    logoMesh.rotation.y = Math.PI;
    logoMesh.position.set(0, screenH / 2 - logoH / 2 - 0.35, -slabD / 2 - 0.01);
    device.add(logoMesh);
  }).catch(function () { /* Logo optional — Modell bleibt ohne Branding nutzbar */ });

  // Lautsprecher-Lüftungsschlitze auf der unteren Frontblende rechts (wie im Referenzfoto)
  var slotMat = new THREE.MeshStandardMaterial({ color: 0x0a0b0d, roughness: 0.8 });
  for (var i = 0; i < 6; i++) {
    var slotGeo = new THREE.BoxGeometry(0.05, (screenH / 2 - deckH * 0.35) * 0.5, 0.06);
    var slot = new THREE.Mesh(slotGeo, slotMat);
    slot.position.set(screenW * 0.3 + i * 0.1, -screenH / 2 + 0.45, slabD / 2 + 0.01);
    device.add(slot);
  }

  device.rotation.x = THREE.MathUtils.degToRad(-24);
  device.position.set(0, 3.9, 0.35);
  scene.add(device);

  // -----------------------------------------
  // Standfuß — Z-förmiger Knickarm (Doppelgelenk) + Schwenk-Hülse + Keilfuß,
  // wie im Referenzfoto (Seiten-/Rückansicht). Als 2D-Profil extrudiert und
  // per geometry.center() selbst-zentriert, um Achsen-Fehler zu vermeiden.
  // -----------------------------------------
  var armWidth = 1.5;
  var armThickness = 0.3;

  var armShape = new THREE.Shape([
    new THREE.Vector2(-armThickness / 2, 0),
    new THREE.Vector2(armThickness / 2, 0),
    new THREE.Vector2(armThickness / 2 + 0.55, 1.3),
    new THREE.Vector2(armThickness / 2 + 0.15, 2.55),
    new THREE.Vector2(-armThickness / 2 + 0.15, 2.55),
    new THREE.Vector2(-armThickness / 2 + 0.55, 1.3)
  ]);
  var armGeo = new THREE.ExtrudeGeometry(armShape, { depth: armWidth, bevelEnabled: true, bevelThickness: 0.02, bevelSize: 0.02, bevelSegments: 1 });
  armGeo.translate(0, 0, -armWidth / 2); // Breite (Extrude-Achse Z) zentrieren, Profil (X/Y) bleibt unverändert
  var arm = new THREE.Mesh(armGeo, standMat);
  // Profil liegt in der lokalen X/Y-Ebene (Fuß bei y=0, Gelenk bei y=2.55) — Rotation um Y
  // ordnet die Extrude-Breite (lokal Z) der Welt-X-Achse zu, Höhe (lokal Y) bleibt Welt-Y.
  arm.rotation.y = -Math.PI / 2;
  arm.position.set(0.1, 0.28, 0.55);
  scene.add(arm);

  // Schwenkgelenk (Zylinder + zwei Schraubenköpfe) zwischen Arm und Bildschirmrückseite —
  // als Kind von "device" in LOKALEN Koordinaten platziert, damit es die Kippung der
  // Baugruppe mitmacht und dadurch immer hinter dem Panel verdeckt bleibt (nicht wie zuvor
  // in festen Weltkoordinaten, wo es aus manchen Blickwinkeln vor dem Display "schwebte").
  var hingeMat = new THREE.MeshStandardMaterial({ color: 0x2a2c30, roughness: 0.45, metalness: 0.6 });
  var hingeGeo = new THREE.CylinderGeometry(0.5, 0.5, 1.55, 20);
  var hinge = new THREE.Mesh(hingeGeo, hingeMat);
  hinge.rotation.z = Math.PI / 2;
  hinge.position.set(0.1, -0.95, -slabD / 2 - 0.55);
  device.add(hinge);

  var boltGeo = new THREE.CylinderGeometry(0.13, 0.13, 0.08, 16);
  [-0.78, 0.78].forEach(function (dx) {
    var bolt = new THREE.Mesh(boltGeo, hingeMat);
    bolt.rotation.z = Math.PI / 2;
    bolt.position.set(0.1 + dx, -0.95, -slabD / 2 - 0.55);
    device.add(bolt);
  });

  // Fußplatte — keilförmiger Umriss (vorne spitz zulaufend, mit Einkerbung), extrudiert
  var baseShape = new THREE.Shape();
  baseShape.moveTo(-1.65, -1.05);
  baseShape.lineTo(1.65, -1.05);
  baseShape.lineTo(1.55, 0.75);
  baseShape.lineTo(0.35, 1.35);
  baseShape.lineTo(0.12, 1.05);
  baseShape.lineTo(-0.12, 1.05);
  baseShape.lineTo(-0.35, 1.35);
  baseShape.lineTo(-1.55, 0.75);
  baseShape.closePath();
  var baseGeo = new THREE.ExtrudeGeometry(baseShape, { depth: 0.3, bevelEnabled: true, bevelThickness: 0.04, bevelSize: 0.04, bevelSegments: 2 });
  baseGeo.translate(0, 0, -0.15); // Extrude-Tiefe (Z) zentrieren
  baseGeo.rotateX(-Math.PI / 2);
  var base = new THREE.Mesh(baseGeo, standMat);
  base.position.set(0.1, 0.3, -0.15);
  scene.add(base);

  // Bodenschatten (weiche Ellipse statt echter Shadow-Map, performant & stilvoll)
  var shadowTex = (function () {
    var c = document.createElement('canvas');
    c.width = c.height = 256;
    var ctx = c.getContext('2d');
    var g = ctx.createRadialGradient(128, 128, 10, 128, 128, 128);
    g.addColorStop(0, 'rgba(0,0,0,0.35)');
    g.addColorStop(1, 'rgba(0,0,0,0)');
    ctx.fillStyle = g;
    ctx.fillRect(0, 0, 256, 256);
    return new THREE.CanvasTexture(c);
  })();
  var shadowMat = new THREE.MeshBasicMaterial({ map: shadowTex, transparent: true, depthWrite: false });
  var shadowMesh = new THREE.Mesh(new THREE.PlaneGeometry(6, 4.5), shadowMat);
  shadowMesh.rotation.x = -Math.PI / 2;
  shadowMesh.position.set(0.1, 0.02, -0.15);
  scene.add(shadowMesh);

  // -----------------------------------------
  // Steuerung
  // -----------------------------------------
  var controls = new OrbitControls(camera, renderer.domElement);
  controls.target.copy(focus);
  controls.enableDamping = true;
  controls.dampingFactor = 0.08;
  controls.minDistance = 8;
  controls.maxDistance = 22;
  controls.minPolarAngle = THREE.MathUtils.degToRad(35);
  controls.maxPolarAngle = THREE.MathUtils.degToRad(95);
  controls.enablePan = false;
  controls.autoRotate = true;
  controls.autoRotateSpeed = 1.4;
  controls.update();

  mount.addEventListener('pointerdown', function () { controls.autoRotate = false; });

  function onResize() {
    var w = mount.clientWidth, h = mount.clientHeight;
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
    renderer.setSize(w, h);
  }
  window.addEventListener('resize', onResize);

  var ro = new ResizeObserver(onResize);
  ro.observe(mount);

  function animate() {
    requestAnimationFrame(animate);
    controls.update();
    renderer.render(scene, camera);
  }

  // Erst starten, wenn die Sektion sichtbar ist (spart Ressourcen)
  var started = false;
  var io = new IntersectionObserver(function (entries) {
    if (entries[0].isIntersecting && !started) {
      started = true;
      animate();
      io.disconnect();
    }
  }, { threshold: 0.1 });
  io.observe(mount);
}
