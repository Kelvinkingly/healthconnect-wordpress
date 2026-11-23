'use strict';
document.querySelectorAll('[data-filter]').forEach(input=>{
 const key=input.dataset.filter, directory=document.querySelector(`[data-directory="${key}"]`), count=document.querySelector(`[data-count="${key}"]`);
 const update=()=>{const terms=input.value.trim().toLowerCase().split(/\s+/);let n=0;directory.querySelectorAll('.searchable').forEach(card=>{const match=terms.every(t=>card.dataset.search.toLowerCase().includes(t));card.hidden=!match;if(match)n++;});count.textContent=`${n} matching ${n===1?'result':'results'}`;};
 input.addEventListener('input',update);document.querySelector(`[data-clear="${key}"]`).addEventListener('click',()=>{input.value='';update();input.focus();});update();
});
document.querySelector('#appointment-preview')?.addEventListener('submit',event=>{event.preventDefault();const status=document.querySelector('#appointment-status');status.hidden=false;status.textContent='Your appointment request preview is ready. No information was transmitted or stored, and no appointment has been booked.';});
