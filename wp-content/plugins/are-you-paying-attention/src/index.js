wp.blocks.registerBlockType("ourplugin/are-you-paying-attention",{
    title: "Are You Paying Attention",
    icon: "Smiley",
    category:"common",
    attributes:{
        skyColor: {type:"string"},
        grassColor: {type: "string"}

    },
    edit: function(props){

        function updateSkyColor(event){
            props.setAttributes({skyColor: event.target.value});
         
        }

        function updateGrassColor(event){
            props.setAttributes({grassColor: event.target.value})
        }



        return (
            <div>
            <input type="text" name="" id="" placeholder = "sky color" value={props.attributes.skyColor} onChange={updateSkyColor}/>
            <input type="text" name="" id="" placeholder = "Grass Color" value={props.attributes.grassColor} onChange={updateGrassColor}/>
            
            </div>
        )
    },

    save: function(props){
        return (
            <h3>!!!!!!Today the sky is ;jsdf;jhdsf;jhds; {props.attributes.skyColor} and the grass is {props.attributes.grassColor}.</h3>
        )

    },
    deprecated:[{

        attributes:{
            skyColor: {type:"string"},
            grassColor: {type: "string"},

        },
    save: function(props){
        return (
            <p>Today the sky is ;jsdf;jhdsf;jhds; {props.attributes.skyColor} and the grass is {props.attributes.grassColor}.</p>
        )
    }
    }, {
        attributes:{
            skyColor: {type:"string"},
            grassColor: {type: "string"},

    }, 
    save: function(props){
        return (
            <p>Today the sky is {props.attributes.skyColor} and the grass is {props.attributes.grassColor}.</p>
        )

    }

    }]
})
 