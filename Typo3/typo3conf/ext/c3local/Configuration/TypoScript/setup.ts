## language files ##
<INCLUDE_TYPOSCRIPT: source="DIR:EXT:c3local/Configuration/TypoScript/languages/" extensions="ts">

#RTE.default.proc {
#    # no remapping should happen, tags should stay as they are
#    HTMLparser_rte {
#        tags {
#            b.remap >
#            i.remap >
#        }
#    }
#    # no remapping should happen, tags should stay as they are
#    HTMLparser_db {
#        tags {
#            strong.remap >
#            em.remap >
#        }
#    }
#}

page {
    includeJSFooter {
        pageTransition = EXT:c3local/Resources/Public/JavaScript/PageTransition.js
        main = EXT:c3local/Resources/Public/JavaScript/main.js
    }

    includeCSS {
        font = https://use.typekit.net/awl2ogt.css
        local = EXT:c3local/Resources/Public/Style/local.css
    }

    30.wrap = <main class="content">|</main>

    stdWrap.wrap = <div class="page_wrap">|</div>

    headerData {
        40 = TEXT
        40.value = <meta name="theme-color" content="#003E5C" />
        45 = TEXT
        45.value (
<script type="text/javascript">
        if ('serviceWorker' in navigator) {
              navigator.serviceWorker.register('service-worker.js', {})
              .then((reg) => {
                // registration worked
                console.log('Registration succeeded. Scope is ' + reg.scope);
              }).catch((error) => {
                // registration failed
                console.log('Registration failed with ' + error);
              });
        }
</script>
        )
    }

    footerData {


        500 = SVG
        500 {
            src = EXT:c3local/Resources/Public/Images/sprite.svg
            renderMode = inline
            id = sprite
        }
    }
}


#################
##
##    Templating
##
##################

plugin.tx_powermail {
    view {
        templateRootPaths {
            10 = EXT:c3local/Resources/Private/powermail/Templates/
        }

        partialRootPaths {
            10 = EXT:c3local/Resources/Private/powermail/Partials/
        }

        layoutRootPaths {
            10 = EXT:c3local/Resources/Private/powermail/Layouts/
        }
    }
}

plugin.tx_news {
    view {
        templateRootPaths {
            10 = EXT:c3local/Resources/Private/news/Templates/
        }

        partialRootPaths {
            10 = EXT:c3local/Resources/Private/news/Partials/
        }

        layoutRootPaths {
            10 = EXT:c3local/Resources/Private/news/Layouts/
        }
    }
}

plugin.tx_femanager {
    persistence.storagePid = 3

    view {
        templateRootPaths.20 = EXT:c3local/Resources/Private/FEManager/Templates/
        layoutRootPaths.20 = EXT:c3local/Resources/Private/FEManager/Layouts/
        partialRootPaths.20 = EXT:c3local/Resources/Private/FEManager/Partials/
    }

    settings {
        new {
            validation {
                name.required = 1
                username.required = 0
                password_repeat.required = 0
                password_repeat.sameAs >
            }

            misc.keepPasswordIfEmpty = 1
            fillEmailWithUsername = 1 # username = email

            email {
                createUserConfirmation {
                    sender {
                        email = TEXT
                        email.value = baxi@c3-coding.de

                        name = TEXT
                        name.value = Fahrmit BAXI
                    }

                    subject >
                    subject = TEXT
                    subject.value = Bestätigen Sie Ihr BAXI Konto

                    priority.value = 2
                }
            }

        }
    }

    _LOCAL_LANG.de {
        submitNew = Konto erstellen
        tx_femanager_domain_model_user.terms = Ich stimme der Datenschutzerklärung zu.
        tx_femanager_domain_model_user.fullname = Name
        tx_femanager_domain_model_user.name = Name
        tx_femanager_domain_model_user.email = E-Mail
        tx_femanager_domain_model_user.ticket_art = Kategorie
        validationErrorRequired = Feld %s ist ein Pflichtfeld
        validationError = Fehler

    }
}

plugin.tx_felogin_pi1 {
    templateFile = EXT:c3local/Resources/Private/FELogin/Resources/Private/Templates/FrontendLogin.html

    welcomeHeader_stdWrap.wrap = <h1>|</h1>
    welcomeMessage_stdWrap.wrap = <p class="align-center">|</p>

    _LOCAL_LANG.de {
        ll_forgot_header = Passwort vergessen?
        login = Anmelden
        ll_forgot_reset_message = Geben Sie ihre E-Mail Adresse ein. Die Instruktionen zum zurücksetzen des Passwortes erhalten Sie per E-Mail
        ll_forgot_reset_message_emailSent = Sie erhalten in kurze eine E-Mail mit dem Link zum zurücksetzen des Passwortes ...
        reset_password = Passwort zurücksetzen
        ll_enter_your_data = E-Mail Adresse
        ll_change_password_header = Neues Passwort setzen
        change_password = Passwort ändern
        newpassword_label1 = neues Passwort
        newpassword_label2 = Passwort wiederholen
        ll_change_password_message = Geben Sie bitte Ihr Passwort 2x ein. Das Passwort sollte mindestens %s Zeichen lang sein.
        ll_change_password_done_message = Ihr neues Passwort wurde gespeichert. Sie können Sich jetzt mit dem Passwort anmelden.
    }
}

lib {
    c3ContentElement {
        layoutRootPaths {
            10 = EXT:c3local/Resources/Private/c3_styled_content/Layouts/
        }

        partialRootPaths {
            10 = EXT:c3local/Resources/Private/c3_styled_content/Partials/
        }

        templateRootPaths {
            10 = EXT:c3local/Resources/Private/c3_styled_content/Templates/
        }
    }

    contentElement {
        templateRootPaths {
            10 = EXT:c3local/Resources/Private/fluid_styled_content/Templates/
        }

        partialRootPaths {
            10 = EXT:c3local/Resources/Private/fluid_styled_content/Partials/
        }

        layoutRootPaths {
            10 = EXT:c3local/Resources/Private/fluid_styled_content/Layouts/
        }
    }

    pageTemplates {
        layoutRootPaths {
            10 = EXT:c3local/Resources/Private/PageTemplates/Layouts/
        }

        partialRootPaths {
            10 = EXT:c3local/Resources/Private/PageTemplates/Partials/
        }

        templateRootPaths {
            10 = EXT:c3local/Resources/Private/PageTemplates/Templates/
        }
    }

    gridelementTemplates {
        layoutRootPaths {
            10 = EXT:c3local/Resources/Private/gridElements/Layouts/
        }

        partialRootPaths {
            10 = EXT:c3local/Resources/Private/gridElements/Partials/
        }

        templateRootPaths {
            10 = EXT:c3local/Resources/Private/gridElements/Templates/
        }
    }
}

config.tx_extbase.objects{
    TYPO3\CMS\Extbase\Domain\Model\FileReference {
        className = C3\C3local\Domain\FileReference
    }
}