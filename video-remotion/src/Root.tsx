import { IchtGutWasWarDeinVorschlagUmEs } from "./IchtGutWasWarDeinVorschlagUmEs";
import { Composition } from "remotion";
import { Einreihig, LAENGE } from "./Einreihig";

export const RemotionRoot: React.FC = () => {
  return (
    <>
      <Composition
        id="Einreihig"
        component={Einreihig}
        durationInFrames={LAENGE}
        fps={30}
        width={1280}
        height={720}
      />
      <Composition
        id="icht-gut-was-war-dein-vorschlag-um-es"
        component={IchtGutWasWarDeinVorschlagUmEs}
        durationInFrames={306}
        fps={30}
        width={1280}
        height={720}
      />
    </>
  );
};
