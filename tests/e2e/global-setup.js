// Runs once before all the specs: the site starts from the baseline.
const { resetSite } = require( './support/reset' );

module.exports = async () => {
	resetSite();
};
