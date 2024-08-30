/**
 * All config. options available here:
 * https://cookieconsent.orestbida.com/reference/configuration-reference.html
 */
CookieConsent.run({

	categories: {
		necessary: {
			enabled: true,  // this category is enabled by default
			readOnly: true  // this category cannot be disabled
		},
		analytics: {}
	},

	language: {
		default: 'en',
		autoDetect: 'document',
		translations: {
			en: {
				consentModal: {
					title: 'We use cookies',
					description: 'Cookie modal description',
					acceptAllBtn: 'Accept all',
					acceptNecessaryBtn: 'Reject all',
					showPreferencesBtn: 'Manage Individual preferences'
				},
				preferencesModal: {
					title: 'Manage cookie preferences',
					acceptAllBtn: 'Accept all',
					acceptNecessaryBtn: 'Reject all',
					savePreferencesBtn: 'Accept current selection',
					closeIconLabel: 'Close modal',
					sections: [
						{
							title: 'Somebody said ... cookies?',
							description: 'I want one!'
						},
						{
							title: 'Strictly Necessary cookies',
							description: 'These cookies are essential for the proper functioning of the website and cannot be disabled.',
							//this field will generate a toggle linked to the 'necessary' category
							linkedCategory: 'necessary'
						},
						{
							title: 'Performance and Analytics',
							description: 'These cookies collect information about how you use our website. All of the data is anonymized and cannot be used to identify you.',
							linkedCategory: 'analytics'
						},
						{
							title: 'More information',
							description: 'For any queries in relation to my policy on cookies and your choices, please <a href="#contact-page">contact us</a>'
						}
					]
				}
			},
			it: {
				consentModal: {
					title: 'Questo sito usa dei cookies',
					description: 'Cookie modal description',
					acceptAllBtn: 'Accetta tutti',
					acceptNecessaryBtn: 'Rifiuta tutti',
					showPreferencesBtn: 'Gestisci le preferenze individualmente'
				},
				preferencesModal: {
					title: 'Gestisci le preferenze dei cookies',
					acceptAllBtn: 'Accetta tutti',
					acceptNecessaryBtn: 'Rifiuta tutti',
					savePreferencesBtn: 'Accetta la selezione corrente',
					closeIconLabel: 'Chiudi',
					sections: [
						{
							title: 'Qualcuno ha detto... cookies?',
							description: 'Ne voglio uno!'
						},
						{
							title: 'Cookies strettamente necessari',
							description: 'These cookies are essential for the proper functioning of the website and cannot be disabled.',
							//this field will generate a toggle linked to the 'necessary' category
							linkedCategory: 'necessary'
						},
						{
							title: 'Prestazioni e Analytics',
							description: 'Questi cookie sono essenziali per il corretto funzionamento del sito web e non possono essere disattivati.',
							linkedCategory: 'analytics'
						},
						{
							title: 'Maggiori informazioni',
							description: 'Per qualsiasi domanda relativa alla mia politica sui cookie e alle tue scelte, ti preghiamo di <a href="#contact-page">contattarci</a>'
						}
					]
				}
			},
		}
	}
});