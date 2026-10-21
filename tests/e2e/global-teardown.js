// Runs once after all the specs: the site is left in the baseline state.
const { resetSite } = require( './support/reset' );

module.exports = async () => {
	resetSite();
};
