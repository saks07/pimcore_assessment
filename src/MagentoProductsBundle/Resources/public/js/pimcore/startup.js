pimcore.registerNS("pimcore.plugin.MagentoProductsBundle");

pimcore.plugin.MagentoProductsBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.MagentoProductsBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("MagentoProductsBundle ready!");
    }
});

var MagentoProductsBundlePlugin = new pimcore.plugin.MagentoProductsBundle();
