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
        this.$palette = $(".imagecolor-palette", this.$container);
        this.$elementSelect = this.$image.data("elementSelect");
    
        this.$elementSelect.on("selectElements", $.proxy(this.handleImageSelect, this));
        this.$elementSelect.on("removeElements", $.proxy(this.handleImageDeselect, this));
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
                        this.applyColor(response.color);
                        this.buildPalette([response.color, ...response.palette]);
                    }
                }, this)
            )
        }
    },

    handleImageDeselect: function() {
        $("span", this.$palette).remove();
    },

    buildPalette: function(palette) {
        $("span", this.$palette).remove();

        for (var i=0; i<palette.length; i++) {
            let colorElem = $("<span>");
            colorElem.css({
                "--palette-color": palette[i]
            });
            colorElem.on("click", $.proxy(this.applyColor, this, palette[i]));
            this.$palette.append(colorElem);
        }
    },

    applyColor: function(color) {
        this.$color.val(color);
        this.$color.trigger("change");
    }
});