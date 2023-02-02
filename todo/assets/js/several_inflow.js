
let severalInflow = document.querySelector("#several_cash_in_flows_targets") || false;
let severalOutflow = document.querySelector("#several_cash_out_flows_targets") || false;

let activeElement = severalInflow || severalOutflow;

if(activeElement){
  formCheck = activeElement.querySelectorAll(".form-check");
  formCheck.forEach((element, index)=> {
    //On clone l'element precedent, on cree un nouveau element et on y introduit l'ancie element
    let elementId = severalInflow ? "#several_cash_in_flows_targets_" : "#several_cash_out_flows_targets_";
    newElt = Object.assign(element);
    activeElement.removeChild(element);
    let div = document.createElement("div");
    div.setAttribute("class", "p-2 border-top")
    div.appendChild(newElt);
    activeElement.appendChild(div)
    element.querySelector(elementId + (index + 1)).addEventListener("change", function(e){
      e.preventDefault();
      if(e.target.checked){
        element.parentNode.classList.add("bg-edit")
      }else{
        element.parentNode.classList.remove("bg-edit")
      }
    });
  });
}
