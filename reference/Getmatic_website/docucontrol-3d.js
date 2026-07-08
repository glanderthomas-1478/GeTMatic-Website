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
  scene.add(new THREE.AmbientLight(0xffffff, 0.55));

  var key = new THREE.DirectionalLight(0xffffff, 1.4);
  key.position.set(8, 10, 6);
  scene.add(key);

  var fill = new THREE.DirectionalLight(0x9fd8d8, 0.5);
  fill.position.set(-8, 4, -4);
  scene.add(fill);

  var rim = new THREE.DirectionalLight(0x5ad7d7, 0.6);
  rim.position.set(0, 6, -10);
  scene.add(rim);

  // -----------------------------------------
  // Materialien
  // -----------------------------------------
  var bodyMat = new THREE.MeshPhysicalMaterial({
    color: 0x14161a,
    roughness: 0.35,
    metalness: 0.1,
    clearcoat: 0.6,
    clearcoatRoughness: 0.25
  });

  var screenMat = new THREE.MeshPhysicalMaterial({
    color: 0x05070a,
    roughness: 0.12,
    metalness: 0.0,
    clearcoat: 1.0,
    clearcoatRoughness: 0.08
  });

  var caseMat = new THREE.MeshStandardMaterial({
    color: 0x3c3f44,
    roughness: 0.55,
    metalness: 0.55
  });

  var standMat = new THREE.MeshStandardMaterial({
    color: 0x1c1d1f,
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

  function makeFrontTexture(logo) {
    var c = document.createElement('canvas');
    c.width = 1024; c.height = 640;
    var ctx = c.getContext('2d');
    ctx.fillStyle = '#05070a';
    ctx.fillRect(0, 0, c.width, c.height);
    ctx.strokeStyle = 'rgba(90,215,215,0.35)';
    ctx.lineWidth = 4;
    ctx.strokeRect(40, 40, c.width - 80, c.height - 80);
    var logoW = 440, logoH = logoW * (logo.height / logo.width);
    ctx.drawImage(logo, (c.width - logoW) / 2, 80, logoW, logoH);
    ctx.fillStyle = 'rgba(255,255,255,0.92)';
    ctx.font = '600 46px Arial';
    ctx.textAlign = 'left';
    ctx.fillText('DocuControl', 70, c.height - 90);
    return new THREE.CanvasTexture(c);
  }

  function makeBackTexture(logo) {
    // Logo nahe der Oberkante platziert, damit es nicht von der
    // aufgesetzten Recheneinheit (unterer/mittlerer Bereich) verdeckt wird.
    var c = document.createElement('canvas');
    c.width = 1024; c.height = 640;
    var ctx = c.getContext('2d');
    ctx.fillStyle = '#14161a';
    ctx.fillRect(0, 0, c.width, c.height);
    var logoW = 360, logoH = logoW * (logo.height / logo.width);
    ctx.drawImage(logo, (c.width - logoW) / 2, 55, logoW, logoH);
    return new THREE.CanvasTexture(c);
  }

  // -----------------------------------------
  // Geometrie-Gruppe (Bildschirm + Recheneinheit + Standfuß)
  // -----------------------------------------
  var device = new THREE.Group();

  var screenW = 8.6, screenH = 5.6, screenD = 0.55;
  var screenGeo = new THREE.BoxGeometry(screenW, screenH, screenD);
  var screenMesh = new THREE.Mesh(screenGeo, [
    bodyMat, bodyMat, bodyMat, bodyMat, screenMat, bodyMat
  ]);
  screenMesh.position.set(0, 0, 0);
  device.add(screenMesh);

  loadLogo().then(function (logo) {
    var frontTex = makeFrontTexture(logo);
    screenMesh.material[4] = new THREE.MeshPhysicalMaterial({
      map: frontTex,
      roughness: 0.1,
      clearcoat: 1.0,
      clearcoatRoughness: 0.08
    });
    var backTex = makeBackTexture(logo);
    screenMesh.material[5] = new THREE.MeshPhysicalMaterial({
      map: backTex,
      emissiveMap: backTex,
      emissive: new THREE.Color(0x2a2a2a),
      emissiveIntensity: 0.6,
      roughness: 0.3,
      clearcoat: 0.5,
      clearcoatRoughness: 0.25
    });
  }).catch(function () { /* Logo optional — Modell bleibt ohne Branding nutzbar */ });

  var caseW = 2.6, caseH = 1.9, caseD = 1.7;
  var caseGeo = new THREE.BoxGeometry(caseW, caseH, caseD);
  var caseMesh = new THREE.Mesh(caseGeo, caseMat);
  caseMesh.position.set(1.6, 0.35, -(screenD / 2 + caseD / 2 - 0.05));
  device.add(caseMesh);

  // Lüftungsschlitze auf der Recheneinheit (dünne dunkle Streifen)
  var slotMat = new THREE.MeshStandardMaterial({ color: 0x101113, roughness: 0.8 });
  for (var i = 0; i < 5; i++) {
    var slotGeo = new THREE.BoxGeometry(caseW * 0.75, 0.08, 0.05);
    var slot = new THREE.Mesh(slotGeo, slotMat);
    slot.position.set(1.6, 0.85 - i * 0.22, caseMesh.position.z + caseD / 2 + 0.03);
    device.add(slot);
  }

  device.rotation.x = THREE.MathUtils.degToRad(-14);
  device.position.y = 3.4;
  scene.add(device);

  // Standfuß: Arm + Fußplatte
  var armGeo = new THREE.BoxGeometry(0.5, 2.6, 0.35);
  var arm = new THREE.Mesh(armGeo, standMat);
  arm.position.set(0.4, 1.7, -0.4);
  arm.rotation.x = THREE.MathUtils.degToRad(8);
  scene.add(arm);

  var baseGeo = new THREE.BoxGeometry(3.2, 0.28, 2.6);
  var base = new THREE.Mesh(baseGeo, standMat);
  base.position.set(0.4, 0.32, 0.1);
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
  shadowMesh.position.set(0.4, 0.02, 0.1);
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
