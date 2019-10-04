if (typeof ImageColor == "undefined") {
    ImageColor = {};
}

ImageColor.Field = Garnish.Base.extend({
    $image: null,
    $color: null,
    $elementSelect: null,
    $container: null,
    $spinner: null,

    inputId: null,
    namespace: null,

    init: function(options) {
        options = JSON.parse(options);

        this.inputId = options.id;
        this.namespace = options.name;

        this.$image = $("#" + options.id + "__image");
        this.$color = $("#" + options.id + "__color-field input");
        this.$container = this.$image.parents(".imagecolor-wrapper");
        this.$elementSelect = this.$image.data("elementSelect");
    
        this.$elementSelect.on("selectElements", $.proxy(this.handleImageSelect, this));
    },

    handleImageSelect: function(e) {
        const assetId = e.elements[0].id;

        if (assetId) {
            const _this = this;

            Craft.postActionRequest(
                "imagecolor/image-extractor/get-color",
                {
                    assetId: assetId
                },
                $.proxy( function(response, textStatus) {
                    if (textStatus == "success") {
                        this.$color.val(response.color);
                        this.$color.trigger("change");
                    }
                }, this)
            )
        }
    }
});