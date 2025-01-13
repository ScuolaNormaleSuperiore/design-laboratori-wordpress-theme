CookieConsent.run({

	// root: 'body',
	// autoShow: true,
	// disablePageInteraction: true,
	// hideFromBots: true,
	// mode: 'opt-in',
	// revision: 0,

	cookie: {
			name: 'cc_cookie',
			// domain: location.hostname,
			// path: '/',
			// sameSite: "Lax",
			// expiresAfterDays: 182,
	},

	guiOptions: {
			consentModal: {
					layout: 'cloud inline',
					position: 'bottom center',
					equalWeightButtons: true,
					flipButtons: false
			},
			preferencesModal: {
					layout: 'box',
					equalWeightButtons: true,
					flipButtons: false
			}
	},

	onFirstConsent: ({cookie}) => {
			console.log('onFirstConsent fired',cookie);
	},

	onConsent: ({cookie}) => {
			console.log('onConsent fired!', cookie)
	},

	onChange: ({changedCategories, changedServices}) => {
			console.log('onChange fired!', changedCategories, changedServices);
	},

	onModalReady: ({modalName}) => {
			console.log('ready:', modalName);
	},

	onModalShow: ({modalName}) => {
			console.log('visible:', modalName);
	},

	onModalHide: ({modalName}) => {
			console.log('hidden:', modalName);
	},

	categories: {
			necessary: {
					enabled: true,  // this category is enabled by default
					readOnly: true  // this category cannot be disabled
			},
			analytics: {
					autoClear: {
							cookies: [
									{
											name: '_gid',   // string: exact cookie name
									}
							]
					},
					services: {
							youtube: {
									label: 'Youtube Embed',
									onAccept: () => {},
									onReject: () => {}
							},
					}
			},
			ads: {}
	},

	language: {
			default: 'it',
			translations: {
					it: {
						consentModal: {
								title: 'Personalizza le preferenze di consenso',
								description: 'Utilizziamo i cookie per aiutarti a navigare in modo efficiente e a svolgere determinate funzioni. Troverai informazioni dettagliate su tutti i cookie sotto ogni categoria di consenso qui sotto.',
								acceptAllBtn: 'Accetta tutti',
								acceptNecessaryBtn: 'Rifiuta tutti',
								showPreferencesBtn: 'Gestisci preferenze individuali',
								footer: `
										<a href="/en/legal-notes" target="_blank">Note Legali</a>
										<a href="/en/privacy-policy" target="_blank">Privacy Policy</a>
								`,
						},
						preferencesModal: {
								title: 'Gestisci le preferenze sui cookie',
								acceptAllBtn: 'Accetta tutti',
								acceptNecessaryBtn: 'Rifiuta tutti',
								savePreferencesBtn: 'Accetta la selezione corrente',
								closeIconLabel: 'Chiudi modale',
								serviceCounterLabel: 'Servizio|Servizi',
								sections: [
										{
												title: 'Le tue scelte sulla privacy',
												description: `In questo pannello puoi esprimere alcune preferenze relative al trattamento dei tuoi dati personali. Puoi rivedere e modificare le scelte espresse in qualsiasi momento riemergendo questo pannello tramite il link fornito. Per negare il tuo consenso alle specifiche attività di trattamento descritte di seguito, disattiva i pulsanti o usa il pulsante "Rifiuta tutto" e conferma di voler salvare le tue scelte.`,
										},
										{
												title: 'Strettamente necessari',
												description: 'Questi cookie sono essenziali per il corretto funzionamento del sito web e non possono essere disattivati.',
												linkedCategory: 'necessary'
										},
										{
												title: 'Prestazioni e analisi',
												description: 'Questi cookie raccolgono informazioni su come utilizzi il nostro sito web. Tutti i dati sono resi anonimi e non possono essere utilizzati per identificarti.',
												linkedCategory: 'analytics',
												cookieTable: {
														caption: 'Tabella dei cookies',
														headers: {
																name: 'Cookie',
																domain: 'Domain',
																desc: 'Description'
														},
														body: [
																{
																		name: '_ga',
																		domain: location.hostname,
																		desc: 'Description 1',
																},
																{
																		name: '_gid',
																		domain: location.hostname,
																		desc: 'Description 2',
																}
														]
												}
										},
										{
												title: 'Ulteriori informazioni',
												description: 'Per qualsiasi domanda relativa alla mia politica sui cookie e alle tue scelte, ti preghiamo di <a href="#contact-page">contattarci</a>'
										}
								]
						}
				}
			}
	},

});