# Module configuration
module{
    tx_c3baxi_tools_c3baxibaxi {
        persistence {
            storagePid = {$module.tx_c3baxi_baxi.persistence.storagePid}
        }

        view {
            templateRootPaths.0 = EXT:{extension.extensionKey}/Resources/Private/Backend/Templates/
            templateRootPaths.1 = {$module.tx_c3baxi_baxi.view.templateRootPath}
            partialRootPaths.0 = EXT:c3baxi/Resources/Private/Backend/Partials/
            partialRootPaths.1 = {$module.tx_c3baxi_baxi.view.partialRootPath}
            layoutRootPaths.0 = EXT:c3baxi/Resources/Private/Backend/Layouts/
            layoutRootPaths.1 = {$module.tx_c3baxi_baxi.view.layoutRootPath}
        }


        settings {
            fahrt.maximalePlaetze = 5
            email {
                debug = true
                debugEmail = benes@myc3.com
            }
        }

    }
    tx_femanager {
        view {
            templateRootPath = EXT:c3baxi/Resources/Private/FEManager/Templates/
            templateRootPaths = EXT:c3baxi/Resources/Private/FEManager/Templates/
        }
    }
}

# Plugin settings
plugin {
    tx_c3baxi {
        features {
            requireCHashArgumentForActionArguments = 0
        }

        settings {
            ajax {

            }

            showPrices = 0

            booking.info.options {
                0 {
                    label = Anmerkung wählen
                    value =
                }

                10 {
                    label = Barrierefreier Zugang
                    value = Barrierefreier Zugang
                }

                20 {
                    label = Kindersitz
                    value = Kindersitz
                }
            }
        }
    }

    tx_femanager {

        view {
            partialRootPaths.30 = EXT:c3baxi/Resources/Private/FEManager/Partials/
        }
    }
}

# global setting
config.tx_extbase {
    #plugin.tx_c3baxi {
    persistence {
        classes {
            In2code\Femanager\Domain\Model\User {
                subclasses {
                    0 = C3\C3baxi\Domain\Model\User
                }
            }

            C3\C3baxi\Domain\Model\User {
                mapping {
                    tableName = fe_users
                    recordType = 0
                }
            }

            C3\C3baxi\Domain\Model\Booking {
                mapping {
                    columns.cruser_id.mapOnProperty = cruserId
                    columns.tstamp.mapOnProperty = tstamp
                }
            }

            C3\C3baxi\Domain\Model\TtContent {
                mapping {
                    tableName = tt_content
                    columns {
                        altText.mapOnProperty = altText
                        titleText.mapOnProperty = titleText
                        colPos.mapOnProperty = colPos
                        CType.mapOnProperty = CType
                        starttime.mapOnProperty = starttime
                        endtime.mapOnProperty = endtime
                        header_link.mapOnProperty = headerLink
                    }
                }
            }
        }
    }

    objects {
        In2code\Femanager\Controller\UserBackendController.className = C3\C3baxi\Controller\UserBackendController
        In2code\Femanager\Domain\Repository\UserRepository.className = C3\C3baxi\Domain\Repository\UserRepository
        In2code\Femanager\Controller\NewController.className = C3\C3baxi\Controller\UserNewController
        In2code\Femanager\Controller\EditController.className = C3\C3baxi\Controller\UserEditController
    }
}


page {
    headerData {
        300 = TEXT
        300.value (
<link rel="apple-touch-icon" sizes="180x180" href="/fileadmin/icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/fileadmin/icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/fileadmin/icons/favicon-16x16.png">
<link rel="mask-icon" href="/fileadmin/icons/safari-pinned-tab.svg" color="#5bbad5">
<link rel="shortcut icon" href="/fileadmin/icons/favicon.ico">
<meta name="apple-mobile-web-app-title" content="Fahrmit BAXI">
<meta name="application-name" content="Fahrmit BAXI">
<meta name="msapplication-TileColor" content="#00a300">
<meta name="msapplication-config" content="/fileadmin/icons/browserconfig.xml">
<meta name="theme-color" content="#ffffff">
        )
    }

    includeCSS {
        c3baxiJqueryUi = EXT:c3baxi/Resources/Public/JavaScript/jquery-ui-1.12.1.custom/jquery-ui.css

        c3baxi = EXT:c3baxi/Resources/Public/CSS/default.css
    }

    includeJSFooterlibs {

        #        googleMaps = https://maps.googleapis.com/maps/api/js?key=AIzaSyAXjfun_XWAWG80vdaJobZ4YqopBVo_1lE&libraries=geometry&sensor=false&v=3.27
        googleMaps = https://maps.googleapis.com/maps/api/js?key=AIzaSyC0xlK753ERQJ9vXnmhKD8k3cGZ885-gJ8&libraries=geometry&sensor=false&v=3.27
    }

    includeJSFooter {
        c3baxiJqueryUi = EXT:c3baxi/Resources/Public/JavaScript/jquery-ui-1.12.1.custom/jquery-ui.js
        Store = EXT:c3baxi/Resources/Public/JavaScript/Store.js
        c3baxi = EXT:c3baxi/Resources/Public/JavaScript/default.js
    }
}


ajaxContent = PAGE
ajaxContent {
    typeNum = 666

    10 = USER
    10 {
        userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
        vendorName = C3
        extensionName = C3baxi
        pluginName = BaxiSuche
        controller = Ajax
        action = search
        switchableControllerActions {
            Ajax {
                1 = search
                2 = autocomplete
                3 = stationDetail
                4 = help
                5 = favorites
                6 = ride
                7 = rating
                8 = nearestStation
            }
        }
    }

    # disable header code
    config {
        disableAllHeaderCode = 1
        additionalHeaders {
            10 {
                header = Content-type: application/json, utf-8
                replace = 1
            }
        }

        xhtml_cleaning = 0
        admPanel = 0
        debug = 0
    }
}


deepLinks = PAGE
deepLinks{
    typeNum = 999
    10 = USER
    10 {
        userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
        vendorName = C3
        extensionName = C3baxi
        pluginName = DeepLinks
        controller = Deep
        action = index
        switchableControllerAction {
            Deep {
                1 = index
                2 = confirmRide
            }
        }
    }
    config {
        disableAllHeaderCode = 1
        additionalHeaders {
            10 {
                header = Content-type: application/json, utf-8
                replace = 1
            }
        }

        xhtml_cleaning = 0
        admPanel = 0
        debug = 0
    }
}
