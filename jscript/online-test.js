
let currentQuiz = 0;
let answerEls;

const quiz = document.getElementById("quiz");
const questionEl = document.getElementById("question");
const answerlistEl = document.getElementById("choices_lists");
const submitBtn = document.getElementById("testSubmit");

loadQuiz();

// load the quiz title and its choices
function loadQuiz() {
    const currentQuizData = quizData[currentQuiz]

    questionEl.innerHTML = currentQuizData.question;

    loadAnswers(currentQuizData.answers, currentQuizData.id, currentQuizData.answer_unique_key);

    answerEls = document.querySelectorAll('.answer');
    
    submit_btn_text_change();
}

//  load the set of choices fir questions
function loadAnswers(object, quesID, uniquekeyObject) {
    for (const key in object) {
        liEle = '<li class="choiceLi">';
        liEle += '<input type="radio" name="quizChecked[' + quesID + ']" id="questionChoice' + uniquekeyObject[
            key] + '" value="' + object[key] + '" class="answer">';
        liEle += '<label for="questionChoice' + uniquekeyObject[key] + '" id="a_text">' + key + '</label></li>';
        answerlistEl.innerHTML += liEle;
    }
}

// change the name of submit btn text
function submit_btn_text_change() {
    const last_test_number = quizData.length - 1 ;

    if (quizData.length === 1 || currentQuiz === last_test_number) {
        return submitBtn.innerHTML = "Submit";
    }
    
    if (quizData.length > 1 && currentQuiz < quizData.length) {
        return submitBtn.innerHTML = "Next";
    }
}


// get the selected input id and set the attribut to be checked
function getSelected() {
    let answer;

    answerEls.forEach((answerEl) => {
        if (answerEl.checked) {
            // set the checkbox selected
            answerEl.setAttribute("checked", "");
            answer = answerEl.id;
        }
    });

    return answer;
}

// while clicking on the next button hide the previous list 
function hidePreviousListElement() {
    const choiceLi = document.querySelectorAll('.choiceLi');

    choiceLi.forEach((ele) => (ele.hidden = true));
}

// apply the submit or next test based on click
submitBtn.addEventListener("click", (e) => {
    e.preventDefault();

    const answer = getSelected();
    hidePreviousListElement();

    currentQuiz++;

    if (currentQuiz < quizData.length) {
        loadQuiz();
    } else {
        questionEl.innerHTML = "Test Submitted Successfully";
        submitBtn.disabled = true;
        submitBtn.hidden = true;
        document.getElementById("formSubmit").submit();
    }
});