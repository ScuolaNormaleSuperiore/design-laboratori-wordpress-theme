/**
 * All config. options available here:
 * https://cookieconsent.orestbida.com/reference/configuration-reference.html
 */
CookieConsent.run({

	categories: {
		necessary: {
			enabled: true,
			readOnly: true,
		},
		functional: {
			enabled: false,
			readOnly: false,
		},
		analytics: {
			enabled: false,
			readOnly: false,
		},
		performance: {
			enabled: false,
			readOnly: false,
		},
		advertisement: {
			enabled: false,
			readOnly: false,
		},
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
							description: 'Necessary cookies are required to enable the basic features of this site, such as providing secure log-in or adjusting your consent preferences. These cookies do not store any personally identifiable data.',
							linkedCategory: 'necessary'
						},
						{
							title: 'Functional cookies',
							description: 'Functional cookies help perform certain functionalities like sharing the content of the website on social media platforms, collecting feedback, and other third-party features.',
							linkedCategory: 'functional'
						},
						{
							title: 'Analytics cookies',
							description: 'Analytical cookies are used to understand how visitors interact with the website. These cookies help provide information on metrics such as the number of visitors, bounce rate, traffic source, etc.',
							linkedCategory: 'analytics'
						},
						{
							title: 'Performance cookies',
							description: 'Performance cookies are used to understand and analyze the key performance indexes of the website which helps in delivering a better user experience for the visitors.',
							linkedCategory: 'performance'
						},
						{
							title: 'Advertisement cookies',
							description: 'Advertisement cookies are used to provide visitors with customized advertisements based on the pages you visited previously and to analyze the effectiveness of the ad campaigns.',
							linkedCategory: 'advertisement'
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
					description: 'Modulo informativo sui cookies',
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
							title: 'Cookie strettamente necessari',
							description: 'I cookie necessari sono richiesti per abilitare le funzionalità di base di questo sito, come fornire un accesso sicuro o modificare le preferenze di consenso. Questi cookie non memorizzano dati personali identificabili.',
							linkedCategory: 'necessary'
						},
						{
							title: 'Cookie funzionali',
							description: 'I cookie funzionali aiutano a svolgere determinate funzionalità, come la condivisione del contenuto del sito web su piattaforme di social media, la raccolta di feedback e altre funzionalità di terze parti.',
							linkedCategory: 'functional'
						},
						{
							title: 'Cookie analitici',
							description: 'I cookie analitici vengono utilizzati per comprendere come i visitatori interagiscono con il sito web. Questi cookie aiutano a fornire informazioni su parametri quali il numero di visitatori, il tasso di rimbalzo, la fonte di traffico, ecc.',
							linkedCategory: 'analytics'
						},
						{
							title: 'Cookie pubblicitari',
							description: 'I cookie pubblicitari vengono utilizzati per fornire ai visitatori annunci pubblicitari personalizzati in base alle pagine visitate in precedenza e per analizzare l\'efficacia delle campagne pubblicitarie.',
							linkedCategory: 'advertisement'
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