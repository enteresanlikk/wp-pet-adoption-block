wp.blocks.registerBlockType("pet-adoption/list", {
  title: "Pet Adoption List",
  edit: function () {
    return wp.element.createElement("div", { className: "our-placeholder-block" }, "Pet Adoption List Placeholder")
  },
  save: function () {
    return null
  }
})
