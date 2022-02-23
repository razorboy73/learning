import React, {useState, useEffect} from 'react'
import ReactDOM from 'react-dom'
import "./frontend.scss"

const divsToUpdate = document.querySelectorAll(".paying-attention-update-me")

divsToUpdate.forEach(function(div){
    const data = JSON.parse(div.querySelector("pre").innerHTML)
    ReactDOM.render(<Quiz {...data} />, div) 
    div.classList.remove("paying-attention-update-me")
}) 

function Quiz(props){

    const [isCorrect, setIsCorrect] = useState(undefined)
    const [isCorrectDelayed, setIsCorrectDelayed] = useState(undefined)

    useEffect(() =>{
        if(isCorrect === false){

            setTimeout(() =>{
                setIsCorrect(undefined)
            }, 2600)

        }


        if(isCorrect === true){

            setTimeout(() =>{
                setIsCorrectDelayed(true)
            }, 1000)

        }




    },[isCorrect])





    function handleAnswer(index){
        if(index == props.correctAnswer){
            setIsCorrect(true)
        }else{
           setIsCorrect(false)
        }
    }
    return(
        <div className="paying-attention-frontend">
            <p>{props.question}</p>
            <ul>
                {props.answers.map(function(answer, index){
                    return (<li onClick={isCorrect === true ? undefined : ()=> handleAnswer(index)}>
                            {isCorrectDelayed === true && index == props.correctAnswer &&(
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" className="bi bi-check-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                              </svg>
                            )}
                            {isCorrectDelayed === true && index != props.correctAnswer &&(
                               <svg xmlns="http://www.w3.org/2000/svg" width="124" height="24"  className="bi bi-x-circle" viewBox="0 0 16 16">
                               <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                               <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                             </svg>
                            )}
                            {answer}
                            </li>
                        )
                })}
            </ul>
            <div className={"correct-message" +(isCorrect == true ? " correct-message--visible": "")}>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24
            "  className="bi bi-emoji-smile" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M4.285 9.567a.5.5 0 0 1 .683.183A3.498 3.498 0 0 0 8 11.5a3.498 3.498 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.498 4.498 0 0 1 8 12.5a4.498 4.498 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z"/>
                </svg>
                <p>This is Correct</p>
            </div>
            <div className={"incorrect-message" +(isCorrect === false ? " correct-message--visible": "")}>
          
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"  className="bi bi-emoji-frown-fill" viewBox="0 0 16 16">
  <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm-2.715 5.933a.5.5 0 0 1-.183-.683A4.498 4.498 0 0 1 8 9.5a4.5 4.5 0 0 1 3.898 2.25.5.5 0 0 1-.866.5A3.498 3.498 0 0 0 8 10.5a3.498 3.498 0 0 0-3.032 1.75.5.5 0 0 1-.683.183zM10 8c-.552 0-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5S10.552 8 10 8z"/>
</svg>
                <p>This is not correct you loser</p>
            </div>
        </div>
    ) 
} 