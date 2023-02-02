const addAvalisteToCollection = (e) => {
  const collectionHolder = document.querySelector(
    "." + e.currentTarget.dataset.collectionHolderClass
  );
  const div = document.createElement("div");
  if (collectionHolder.dataset.index < 5) {
    const prototype = collectionHolder.dataset.prototype.replace(
      /__name__/g,
      collectionHolder.dataset.index
    );

    div.classList.add("row", "bg-purple", "mb-2");
    div.id = "avaliste_" + collectionHolder.dataset.index;

    const element = document.createElement("div");
    element.innerHTML = prototype;
    element.querySelectorAll(".form-group").forEach((elt, id) => {
      const col = document.createElement("div");
      cols = id == 0 ? 5 : 6;
      col.classList.add("col-md-" + cols);
      col.innerHTML = elt.outerHTML;
      div.appendChild(col);
    });
    const btnDiv = document.createElement("div");
    btnDiv.classList.add("col-md-1");
    btnDiv.innerHTML =
      '<button class="btn bg-purple btn_remove_avaliste" id="btn_remove_avaliste_' +
      collectionHolder.dataset.index +
      '" data-index="' +
      collectionHolder.dataset.index +
      '"> <i class="fa fa-trash text-danger"></i></button>';
    div.appendChild(btnDiv);
    collectionHolder.appendChild(div);
    document.querySelectorAll(".btn_remove_avaliste").forEach((elt) => {
      if (elt.dataset.index == collectionHolder.dataset.index) {
        elt.style.display = "inline";
      } else {
        elt.style.display = "none";
      }

      elt.addEventListener("click", (e) => {
        e.preventDefault();
        collectionHolder.removeChild("#avaliste_" + elt.dataset.index);
        if (elt.dataset.index > 1) {
          document.querySelectorAll(
            "#btn_remove_avaliste_" + elt.dataset.index - 1
          ).style.display = "inline";
        }
      });
      collectionHolder.dataset.index++;
      toggleBtn(e.currentTarget, collectionHolder.dataset.index);
    });
  }
};

function toggleBtn(BtnNode, index) {
  if (index == 5) {
    BtnNode.style.display = "none";
  } else {
    BtnNode.style.display = "inline";
  }
}

document
  .querySelector("#btn_add_avaliste")
  .addEventListener("click", addAvalisteToCollection);

$(".debt_loan_in_flows").on("change", function () {
  let interestElement = $(".debt_interests");
  let interest = parseInt(
    parseInt($(this).val(), 10) *
      parseFloat(interestElement.attr("data-rate"), 10),
    10
  );
  interestElement.attr(
    "placeHolder",
    interest + " " + interestElement.attr("data-devise")
  );
});
