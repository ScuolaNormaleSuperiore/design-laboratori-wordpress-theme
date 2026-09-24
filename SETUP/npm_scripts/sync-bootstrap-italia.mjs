// Sincronizza gli asset di Bootstrap Italia (css/js/svg/fonts) dalla
// dipendenza npm alla cartella assets/bootstrap-italia/ effettivamente
// caricata dal tema (assets/bootstrap-italia/ e' committata in git, non
// e' un semplice mirror di node_modules: va risincronizzata esplicitamente
// dopo ogni bump della versione). Stesso approccio già validato nel
// porting statico bs-playground (scripts/sync-bootstrap-italia.mjs).
import { cp, mkdir, readFile, rename, rm } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const themeRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..', '..');
const packageRoot = resolve(themeRoot, 'node_modules/bootstrap-italia');
const source = resolve(packageRoot, 'dist');
const target = resolve(themeRoot, 'assets/bootstrap-italia');
const staging = resolve(themeRoot, 'assets/.bootstrap-italia.tmp');

const expectedVersion = '3.0.0-beta.7';
const packageMetadata = JSON.parse(
  await readFile(resolve(packageRoot, 'package.json'), 'utf8'),
);

if (packageMetadata.version !== expectedVersion) {
  throw new Error(
    `Versione Bootstrap Italia inattesa: ${packageMetadata.version}; attesa: ${expectedVersion}. Aggiorna la costante expectedVersion in questo script insieme alla dipendenza in package.json.`,
  );
}

await mkdir(dirname(target), { recursive: true });
await rm(staging, { recursive: true, force: true });
await cp(source, staging, { recursive: true });
await rm(target, { recursive: true, force: true });
await rename(staging, target);

console.log(`Asset Bootstrap Italia ${expectedVersion} copiati in assets/bootstrap-italia`);
