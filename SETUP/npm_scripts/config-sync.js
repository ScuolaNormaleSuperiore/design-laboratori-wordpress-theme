const replace = require('replace-in-file')
const fs = require('fs')
const path = require('path')

// package.json is updated by `npm version major/minor/patch` or similar.
// The require path is relative to this script file, not to the working directory.
const version = require('../../package.json').version

const projectRoot = path.join(__dirname, '..', '..')

const cssPath = path.join(projectRoot, 'style.css')
const publiccodePath = path.join(projectRoot, 'publiccode.yml')
const versionPath = path.join(projectRoot, 'VERSION.txt')
const changelogPath = path.join(projectRoot, 'CHANGELOG.md')

// The release date is the date of the CHANGELOG.md entry of this version ("## [2.0.2] - 2026-10-20").
const escapedVersion = version.replace(/\./g, '\\.')
const changelogEntry = fs
  .readFileSync(changelogPath, 'utf8')
  .match(new RegExp(`^##\\s*\\[${escapedVersion}\\]\\s*-\\s*(\\d{4}-\\d{2}-\\d{2})`, 'm'))

const configCssOptions = {
  files: cssPath,
  from: /^Version:.*$/gim,
  to: `Version: ${version}`,
}

const configPubliccodeOptions = {
  files: publiccodePath,
  from: /^softwareVersion:.*$/gim,
  to: `softwareVersion: ${version}`,
}

const configReleaseDateOptions = changelogEntry && {
  files: publiccodePath,
  from: /^releaseDate:.*$/gim,
  to: `releaseDate: '${changelogEntry[1]}'`,
}

const replaceInFile = (config) => {
  return replace.sync(config).map((el) => el.file)
}

try {
  let changedFiles = replaceInFile(configCssOptions)
  changedFiles = changedFiles.concat(replaceInFile(configPubliccodeOptions))

  if (configReleaseDateOptions) {
    changedFiles = changedFiles.concat(replaceInFile(configReleaseDateOptions))
  } else {
    console.warn(`CHANGELOG.md has no dated entry for version ${version}: releaseDate in publiccode.yml was not changed.`)
  }

  fs.writeFileSync(versionPath, `${version}\n`)
  changedFiles.push(versionPath)

  console.info('Modified files:', [...new Set(changedFiles)].join(', '))
} catch (error) {
  console.error(error)
  process.exit(1)
}
