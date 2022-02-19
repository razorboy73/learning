wp.blocks.registerBlockType("ourplugin/are-you-paying-attention",{
    title: "Are You Paying Attention",
    icon: "Smiley",
    category:"common",
    edit: function(){
        return (
            <div>
            <p>Hello, this is a paragraph!!!!!</p>
            <h4>Hi there</h4>
            </div>
        )
    },

    save: function(){
        return wp.element.createElement("h1", null, "!This is the front end of JSX!")


    }
})