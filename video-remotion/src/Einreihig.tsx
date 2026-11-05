import { ThreeCanvas } from "@remotion/three";
import { useThree } from "@react-three/fiber";
import React, { useLayoutEffect, useMemo } from "react";
import { Easing, interpolate, useCurrentFrame, useVideoConfig } from "remotion";
import * as THREE from "three";
import { RoomEnvironment } from "three/examples/jsm/environments/RoomEnvironment.js";

// Zeitraster: Alle Bewegungen sind Vielfache von TAKT, dadurch schließt die Schleife exakt.
const TAKT = 9; // Bilder je Getränkekarton
export const LAENGE = 34 * TAKT; // 306 Bilder = 10,2 s
const TRANSIT = 11 * TAKT; // Weg über das Band
const UEBERGABE = 6;
const FALL = 7;
const WECHSEL = 224; // Versandkarton fährt ab
const AB_ENDE = 262;
const ZU_START = 250;

const SPALTEN = 4;
const REIHEN = 3;
const LAGEN = 2;
const PACKS = SPALTEN * REIHEN * LAGEN;

const PACK = { b: 0.145, h: 0.195, t: 0.135 };
const BAHN_Y = 0.62; // Oberkante Rollenbahn
const KARTON = { b: 0.66, h: 0.44, t: 0.48, wand: 0.012 };
const TRICHTER_Y = 1.42;

const FARBE = {
  teal: "#209D9D",
  tealDunkel: "#0F8C8C",
  edelstahl: "#b7c0c2",
  stahlDunkel: "#5d686b",
  band: "#17393b",
  karton: "#b98a5a",
  kartonInnen: "#8f6a43",
};

const bandKurve = new THREE.CatmullRomCurve3([
  new THREE.Vector3(5.2, 3.7, 0),
  new THREE.Vector3(3.3, 3.05, 0),
  new THREE.Vector3(2.0, 2.35, 0),
  new THREE.Vector3(1.15, 1.86, 0),
  new THREE.Vector3(0.62, 1.66, 0),
]);
bandKurve.arcLengthDivisions = 400;

const slot = (k: number) => {
  const lage = Math.floor(k / (SPALTEN * REIHEN));
  const rest = k % (SPALTEN * REIHEN);
  const reihe = Math.floor(rest / SPALTEN);
  const spalte = rest % SPALTEN;
  return {
    x: (spalte - (SPALTEN - 1) / 2) * 0.155,
    y: BAHN_Y + KARTON.wand + PACK.h / 2 + lage * (PACK.h + 0.004),
    z: (reihe - (REIHEN - 1) / 2) * 0.147,
  };
};

const abfahrt = (f: number) =>
  interpolate(f, [WECHSEL, AB_ENDE], [0, 7], {
    easing: Easing.bezier(0.45, 0, 0.8, 0.6),
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });

const zufahrt = (f: number) =>
  interpolate(f, [ZU_START, LAENGE], [-7, 0], {
    easing: Easing.bezier(0.2, 0.5, 0.35, 1),
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });

const useTexturen = () =>
  useMemo(() => {
    const c = document.createElement("canvas");
    c.width = 256;
    c.height = 340;
    const g = c.getContext("2d")!;
    g.fillStyle = "#f6f8f8";
    g.fillRect(0, 0, 256, 340);
    g.fillStyle = FARBE.teal;
    g.beginPath();
    g.moveTo(0, 150);
    g.lineTo(256, 80);
    g.lineTo(256, 210);
    g.lineTo(0, 280);
    g.fill();
    g.fillStyle = "#5AD7D7";
    g.beginPath();
    g.moveTo(0, 280);
    g.lineTo(256, 210);
    g.lineTo(256, 236);
    g.lineTo(0, 306);
    g.fill();
    const t = new THREE.CanvasTexture(c);
    t.colorSpace = THREE.SRGBColorSpace;
    t.anisotropy = 8;
    const packSeite = new THREE.MeshStandardMaterial({ map: t, roughness: 0.42 });
    const packDeckel = new THREE.MeshStandardMaterial({ color: "#d3ecea", roughness: 0.42 });
    return {
      pack: [packSeite, packSeite, packDeckel, packDeckel, packSeite, packSeite],
    };
  }, []);

const Quader: React.FC<{
  p: [number, number, number];
  s: [number, number, number];
  farbe: string;
  metall?: number;
  rau?: number;
  r?: [number, number, number];
  schatten?: boolean;
}> = ({ p, s, farbe, metall = 0, rau = 0.7, r, schatten = true }) => (
  <mesh position={p} rotation={r} castShadow={schatten} receiveShadow>
    <boxGeometry args={s} />
    <meshStandardMaterial color={farbe} metalness={metall} roughness={rau} />
  </mesh>
);

const Halle: React.FC = () => (
  <group>
    <mesh rotation={[-Math.PI / 2, 0, 0]} receiveShadow>
      <planeGeometry args={[40, 30]} />
      <meshStandardMaterial color="#b9c4c4" roughness={0.38} metalness={0.05} />
    </mesh>
    <Quader p={[0, 3.5, -5.5]} s={[40, 7, 0.2]} farbe="#f2f5f5" rau={0.9} schatten={false} />
    <Quader p={[0, 1.15, -5.38]} s={[40, 0.16, 0.04]} farbe={FARBE.teal} rau={0.6} schatten={false} />
    {[-9, -4.5, 0, 4.5, 9].map((x) => (
      <Quader key={x} p={[x, 3.5, -5.2]} s={[0.34, 7, 0.34]} farbe="#dfe5e5" rau={0.8} schatten={false} />
    ))}
    {[-7, -2.3, 2.3, 7].map((x) => (
      <mesh key={x} position={[x, 5.6, -2]}>
        <boxGeometry args={[2.4, 0.05, 0.42]} />
        <meshStandardMaterial color="#ffffff" emissive="#ffffff" emissiveIntensity={1.2} />
      </mesh>
    ))}
  </group>
);

const Rollenbahn: React.FC = () => {
  const rollen = useMemo(() => Array.from({ length: 71 }, (_, i) => -7 + i * 0.2), []);
  return (
    <group>
      <Quader p={[0, BAHN_Y - 0.06, -0.31]} s={[14.4, 0.09, 0.03]} farbe={FARBE.stahlDunkel} metall={0.5} rau={0.45} />
      <Quader p={[0, BAHN_Y - 0.06, 0.31]} s={[14.4, 0.09, 0.03]} farbe={FARBE.stahlDunkel} metall={0.5} rau={0.45} />
      {rollen.map((x) => (
        <mesh key={x} position={[x, BAHN_Y - 0.03, 0]} rotation={[Math.PI / 2, 0, 0]} castShadow receiveShadow>
          <cylinderGeometry args={[0.03, 0.03, 0.6, 12]} />
          <meshStandardMaterial color={FARBE.edelstahl} metalness={0.75} roughness={0.3} />
        </mesh>
      ))}
      {[-6, -4, -2, 2, 4, 6].flatMap((x) =>
        [-0.31, 0.31].map((z) => (
          <Quader key={`${x}${z}`} p={[x, (BAHN_Y - 0.1) / 2, z]} s={[0.05, BAHN_Y - 0.1, 0.05]} farbe={FARBE.stahlDunkel} metall={0.5} rau={0.45} />
        )),
      )}
    </group>
  );
};

const Versandkarton: React.FC<{ x: number }> = ({ x }) => {
  const { b, h, t, wand } = KARTON;
  const lasche = 0.17;
  const neigung = 0.62;
  return (
    <group position={[x, BAHN_Y, 0]}>
      <Quader p={[0, wand / 2, 0]} s={[b, wand, t]} farbe={FARBE.kartonInnen} rau={0.95} />
      <Quader p={[0, h / 2, t / 2]} s={[b, h, wand]} farbe={FARBE.karton} rau={0.95} />
      <Quader p={[0, h / 2, -t / 2]} s={[b, h, wand]} farbe={FARBE.karton} rau={0.95} />
      <Quader p={[b / 2, h / 2, 0]} s={[wand, h, t]} farbe={FARBE.karton} rau={0.95} />
      <Quader p={[-b / 2, h / 2, 0]} s={[wand, h, t]} farbe={FARBE.karton} rau={0.95} />
      <Quader p={[0, h + Math.sin(neigung) * lasche * 0.5, t / 2 + Math.cos(neigung) * lasche * 0.5]} s={[b, wand, lasche]} r={[neigung * -1 + Math.PI, 0, 0]} farbe={FARBE.karton} rau={0.95} />
      <Quader p={[0, h + Math.sin(neigung) * lasche * 0.5, -t / 2 - Math.cos(neigung) * lasche * 0.5]} s={[b, wand, lasche]} r={[neigung, 0, 0]} farbe={FARBE.karton} rau={0.95} />
      <Quader p={[b / 2 + Math.cos(neigung) * lasche * 0.5, h + Math.sin(neigung) * lasche * 0.5, 0]} s={[lasche, wand, t]} r={[0, 0, neigung]} farbe={FARBE.karton} rau={0.95} />
      <Quader p={[-b / 2 - Math.cos(neigung) * lasche * 0.5, h + Math.sin(neigung) * lasche * 0.5, 0]} s={[lasche, wand, t]} r={[0, 0, -neigung]} farbe={FARBE.karton} rau={0.95} />
    </group>
  );
};

const Einlaufband: React.FC<{ f: number }> = ({ f }) => {
  const seg = useMemo(() => {
    const n = 70;
    return Array.from({ length: n }, (_, i) => {
      const u = (i + 0.5) / n;
      const p = bandKurve.getPointAt(u);
      const d = bandKurve.getTangentAt(u);
      return { p, w: Math.atan2(d.y, d.x), l: (bandKurve.getLength() / n) * 1.04 };
    });
  }, []);
  const stuetzen = useMemo(() => [0.2, 0.42, 0.66, 0.86].map((u) => bandKurve.getPointAt(u)), []);
  const nocken = 11 * 2; // zwei Nocken je Takt: läuft nach 9 Bildern deckungsgleich weiter
  return (
    <group>
      {seg.map((s, i) => (
        <group key={i} position={s.p} rotation={[0, 0, s.w]}>
          <Quader p={[0, 0, 0]} s={[s.l, 0.018, 0.2]} farbe={FARBE.band} rau={0.65} />
          <Quader p={[0, 0.02, 0.112]} s={[s.l, 0.09, 0.016]} farbe={FARBE.edelstahl} metall={0.8} rau={0.28} />
          <Quader p={[0, 0.02, -0.112]} s={[s.l, 0.09, 0.016]} farbe={FARBE.edelstahl} metall={0.8} rau={0.28} />
        </group>
      ))}
      {Array.from({ length: nocken }, (_, i) => {
        const u = ((f % TAKT) / TAKT + i) / nocken;
        const p = bandKurve.getPointAt(u % 1);
        const d = bandKurve.getTangentAt(u % 1);
        return (
          <Quader key={i} p={[p.x, p.y + 0.012, p.z]} s={[0.012, 0.006, 0.2]} r={[0, 0, Math.atan2(d.y, d.x)]} farbe={FARBE.tealDunkel} schatten={false} />
        );
      })}
      {stuetzen.map((p, i) => (
        <group key={i}>
          <Quader p={[p.x, p.y / 2, -0.62]} s={[0.07, p.y, 0.07]} farbe={FARBE.stahlDunkel} metall={0.6} rau={0.4} />
          <Quader p={[p.x, p.y - 0.05, -0.36]} s={[0.05, 0.05, 0.52]} farbe={FARBE.stahlDunkel} metall={0.6} rau={0.4} />
        </group>
      ))}
    </group>
  );
};

// Trichterkopf fährt über die aktuelle Packposition
const trichterPos = (f: number) => {
  const q = Math.min(Math.max(f - UEBERGABE, 0), PACKS * TAKT) / TAKT;
  const k = Math.min(Math.floor(q), PACKS - 1);
  const von = slot(k);
  const nach = slot(Math.min(k + 1, PACKS - 1));
  const anteil = interpolate(q - k, [0.35, 1], [0, 1], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
    easing: Easing.inOut(Easing.quad),
  });
  let x = von.x + (nach.x - von.x) * anteil;
  let z = von.z + (nach.z - von.z) * anteil;
  // Rückfahrt zur ersten Position im Kartonwechsel
  const zurueck = interpolate(f, [PACKS * TAKT + UEBERGABE + 4, LAENGE - 10], [0, 1], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
    easing: Easing.inOut(Easing.quad),
  });
  x += (slot(0).x - x) * zurueck;
  z += (slot(0).z - z) * zurueck;
  return { x, z };
};

const Portal: React.FC<{ f: number }> = ({ f }) => {
  const { x, z } = trichterPos(f);
  const stopper = interpolate(f, [WECHSEL - 8, WECHSEL, LAENGE - 22, LAENGE - 14], [0, -0.13, -0.13, 0], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });
  return (
    <group>
      {[
        [-1.0, -0.7],
        [-1.0, 0.7],
        [1.0, -0.7],
        [1.0, 0.7],
      ].map(([px, pz]) => (
        <Quader key={`${px}${pz}`} p={[px, 1.0, pz]} s={[0.08, 2.0, 0.08]} farbe={FARBE.edelstahl} metall={0.75} rau={0.32} />
      ))}
      <Quader p={[0, 1.96, -0.7]} s={[2.08, 0.1, 0.08]} farbe={FARBE.teal} rau={0.4} />
      <Quader p={[0, 1.96, 0.7]} s={[2.08, 0.1, 0.08]} farbe={FARBE.teal} rau={0.4} />
      <Quader p={[-1.0, 1.96, 0]} s={[0.08, 0.1, 1.48]} farbe={FARBE.teal} rau={0.4} />
      <Quader p={[1.0, 1.96, 0]} s={[0.08, 0.1, 1.48]} farbe={FARBE.teal} rau={0.4} />
      {/* Führungsschiene und Trichterkopf */}
      <Quader p={[0, 1.62, -0.28]} s={[2.0, 0.03, 0.03]} farbe={FARBE.stahlDunkel} metall={0.6} rau={0.35} />
      <Quader p={[0, 1.62, 0.28]} s={[2.0, 0.03, 0.03]} farbe={FARBE.stahlDunkel} metall={0.6} rau={0.35} />
      <group position={[x, TRICHTER_Y, z]}>
        <Quader p={[0, 0.0, 0.095]} s={[0.2, 0.16, 0.01]} farbe={FARBE.edelstahl} metall={0.8} rau={0.28} />
        <Quader p={[0, 0.0, -0.095]} s={[0.2, 0.16, 0.01]} farbe={FARBE.edelstahl} metall={0.8} rau={0.28} />
        <Quader p={[0.105, 0.0, 0]} s={[0.01, 0.16, 0.2]} farbe={FARBE.edelstahl} metall={0.8} rau={0.28} />
        <Quader p={[-0.105, 0.0, 0]} s={[0.01, 0.16, 0.2]} farbe={FARBE.edelstahl} metall={0.8} rau={0.28} />
        <Quader p={[0, 0.09, 0.115]} s={[0.24, 0.018, 0.03]} farbe={FARBE.teal} rau={0.4} />
      </group>
      {/* Stopper am Kartonplatz */}
      <mesh position={[0.4, BAHN_Y + 0.05 + stopper, 0.34]} castShadow>
        <cylinderGeometry args={[0.022, 0.022, 0.22, 16]} />
        <meshStandardMaterial color={FARBE.teal} roughness={0.35} />
      </mesh>
    </group>
  );
};

// Versatz senkrecht zur Bandfläche, immer nach oben
const oben = (w: number) => {
  const n = new THREE.Vector3(-Math.sin(w), Math.cos(w), 0);
  if (n.y < 0) n.negate();
  return n.multiplyScalar(0.0091 + PACK.b / 2);
};

// Pack unterwegs: Band, Übergabe zum Trichter, Fall in den Karton
const PackUnterwegs: React.FC<{ k: number; f: number; material: THREE.Material[] }> = ({ k, f, material }) => {
  const start = k * TAKT - TRANSIT;
  const a = ((f - start) % LAENGE + LAENGE) % LAENGE;
  if (a >= TRANSIT + UEBERGABE + FALL) return null;
  const z = slot(k);
  let pos: THREE.Vector3;
  let drehZ = 0;
  if (a < TRANSIT) {
    const u = a / TRANSIT;
    const p = bandKurve.getPointAt(u);
    const d = bandKurve.getTangentAt(u);
    const w = Math.atan2(d.y, d.x);
    pos = p.clone().add(oben(w));
    drehZ = w + Math.PI / 2;
  } else {
    const dEnde = bandKurve.getTangentAt(1);
    const wEnde = Math.atan2(dEnde.y, dEnde.x) + Math.PI / 2;
    const ende = bandKurve.getPointAt(1).add(oben(wEnde - Math.PI / 2));
    const kopf = slot(k);
    if (a < TRANSIT + UEBERGABE) {
      const u = (a - TRANSIT) / UEBERGABE;
      pos = new THREE.Vector3(
        ende.x + (kopf.x - ende.x) * u,
        ende.y + (TRICHTER_Y + 0.05 - ende.y) * u,
        ende.z + (kopf.z - ende.z) * u,
      );
      drehZ = wEnde * (1 - u);
    } else {
      const u = (a - TRANSIT - UEBERGABE) / FALL;
      const g = u * u;
      pos = new THREE.Vector3(
        kopf.x + (z.x - kopf.x) * u,
        TRICHTER_Y + 0.05 + (z.y - (TRICHTER_Y + 0.05)) * g,
        kopf.z + (z.z - kopf.z) * u,
      );
    }
  }
  return (
    <mesh position={pos} rotation={[0, 0, drehZ]} material={material} castShadow>
      <boxGeometry args={[PACK.b, PACK.h, PACK.t]} />
    </mesh>
  );
};

const Umgebung: React.FC = () => {
  const { gl, scene } = useThree();
  useLayoutEffect(() => {
    const pmrem = new THREE.PMREMGenerator(gl);
    scene.environment = pmrem.fromScene(new RoomEnvironment(), 0.04).texture;
    scene.environmentIntensity = 0.38;
    pmrem.dispose();
  }, [gl, scene]);
  return null;
};

const Kamera: React.FC<{ f: number }> = ({ f }) => {
  const { camera } = useThree();
  useLayoutEffect(() => {
    const w = (2 * Math.PI * f) / LAENGE;
    camera.position.set(1.75 + Math.sin(w) * 0.04, 2.75 + Math.cos(w) * 0.012, 3.05);
    camera.lookAt(0.18, 1.12, 0);
    camera.updateProjectionMatrix();
  }, [camera, f]);
  return null;
};

export const Einreihig: React.FC = () => {
  const f = useCurrentFrame();
  const { width, height } = useVideoConfig();
  const tex = useTexturen();
  const kartonX = abfahrt(f);

  return (
    <ThreeCanvas
      width={width}
      height={height}
      shadows
      camera={{ fov: 38, near: 0.1, far: 80, position: [1.75, 2.75, 3.05] }}
      gl={{ antialias: true, preserveDrawingBuffer: true }}
    >
      <Umgebung />
      <Kamera f={f} />
      <color attach="background" args={["#eef3f3"]} />
      <fog attach="fog" args={["#eef3f3", 9, 24]} />
      <hemisphereLight args={["#ffffff", "#aab8b8", 0.5]} />
      <directionalLight
        position={[3.5, 7, 4]}
        intensity={1.55}
        castShadow
        shadow-mapSize={[2048, 2048]}
        shadow-camera-left={-6}
        shadow-camera-right={6}
        shadow-camera-top={6}
        shadow-camera-bottom={-6}
        shadow-bias={-0.0004}
      />
      <directionalLight position={[-4, 3, 5]} intensity={0.35} />

      <Halle />
      <Rollenbahn />
      <Portal f={f} />
      <Einlaufband f={f} />

      {/* Karton in Befüllung bzw. abfahrend, darin die gelandeten Packs */}
      <Versandkarton x={kartonX} />
      {Array.from({ length: PACKS }, (_, k) => {
        if (f < k * TAKT + FALL) return null;
        const z = slot(k);
        return (
          <mesh key={k} position={[z.x + kartonX, z.y, z.z]} material={tex.pack} castShadow receiveShadow>
            <boxGeometry args={[PACK.b, PACK.h, PACK.t]} />
          </mesh>
        );
      })}

      {/* Nächster, leerer Karton fährt zu */}
      {f >= ZU_START ? <Versandkarton x={zufahrt(f)} /> : null}

      {Array.from({ length: PACKS }, (_, k) => (
        <PackUnterwegs key={k} k={k} f={f} material={tex.pack} />
      ))}
    </ThreeCanvas>
  );
};
