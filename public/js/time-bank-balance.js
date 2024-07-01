let balancePresences = document.getElementById('balancePresence');
let balanceAbsence = document.getElementById('balanceAbsence');
let balanceTotal = document.getElementById('balanceTotal')

if(balancePresences.innerHTML.includes('-')){
    balancePresences.style.color = 'red';
}
else{
    balancePresences.style.color = 'green';
}

if(balanceAbsence.innerHTML.includes('-')){
    balanceAbsence.style.color = 'red';

}
else{
    balanceAbsence.style.color = 'green';
    balanceAbsence.classList.add('greenMargin');

}

if(balanceTotal.innerHTML.includes('-')){
    balanceTotal.style.color = 'red';
}
else{
    balanceTotal.style.color = 'green';
    balanceTotal.classList.add('greenMargin');
}
