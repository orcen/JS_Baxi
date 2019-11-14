
module.tx_c3baxi_baxi {
    view {
        # cat=module.tx_c3baxi_baxi/file; type=string; label=Path to template root (BE)
        templateRootPath = EXT:c3baxi/Resources/Private/Backend/Templates/
        # cat=module.tx_c3baxi_baxi/file; type=string; label=Path to template partials (BE)
        partialRootPath = EXT:c3baxi/Resources/Private/Backend/Partials/
        # cat=module.tx_c3baxi_baxi/file; type=string; label=Path to template layouts (BE)
        layoutRootPath = EXT:c3baxi/Resources/Private/Backend/Layouts/
    }
    persistence {
        # cat=module.tx_c3baxi_baxi//a; type=string; label=Default storage PID
        storagePid = 14
    }
}
