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

const replaceInFile = (config) => {
  return replace.sync(config).map((el) => el.file)
}

try {
  let changedFiles = replaceInFile(configCssOptions)
  changedFiles = changedFiles.concat(replaceInFile(configPubliccodeOptions))

  fs.writeFileSync(versionPath, `${version}\n`)
  changedFiles.push(versionPath)

  console.info('Modified files:', changedFiles.join(', '))
} catch (error) {
  console.error(error)
  process.exit(1)
}
