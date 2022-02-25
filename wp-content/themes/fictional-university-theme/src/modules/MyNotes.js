import $ from "jquery";

class MyNotes{
    constructor(){
        this.events();

    }
    events(){

        $(".delete-note").on("click",this.deleNote )

    }

    //Methods will go here

    deleNote(){
        alert("You clicked Delete");
    }

}

export default MyNotes;