const form_element = document.querySelector("#app");
if (form_element) {
  const checkboxes = form_element.querySelectorAll(".form-check-input");
  const amounts = form_element.querySelectorAll(".contributor_amount_input");
  let data = JSON.parse(form_element.getAttribute("data-assistance-info"));

  isEmpty = (value) => {
    return (
      value === undefined ||
      value === null ||
      (typeof value === "object" && Object.keys(value).length === 0) ||
      (typeof value === "string" && value.trim().length === 0)
    );
  };

  class ContributionManager {
    amount;
    part;
    contribution;
    is_amount; //= null;
    total = 0;
    contributors = [];
    indexes = [];
    length = 0;
    inputValues = [];

    getAmount() {
      if (this.hasPart()) {
        this.amount = this.part;
      } else if (this.hasContribution()) {
        this.amount = parseInt(this.contribution / this.length, 10) + 1;
      }
      return this.amount;
    }

    fillAmount(amounts = [], merge = false) {
      let self = this;
      let amount;
      for (const index of this.indexes) {
        amount = this.getAmount();
        let input = this.contributors[index];
        if (input && (this.hasContribution() || this.hasPart())) {
          //if (this.hasAmount()) {
          if (!merge) {
            const cloneInput = input.cloneNode(true);
            input.setAttribute("disabled", true);
            input.setAttribute("data-id-name", input.id);
            input.removeAttribute("name");
            input.removeAttribute("id");
            //input.removeAttribute("class");
            input.removeAttribute("required");
            cloneInput.type = "hidden";
            cloneInput.value = amount;
            input.parentNode.appendChild(cloneInput);
          } else {
            input.setAttribute("disabled", true);
            document.querySelector(
              `#${input.getAttribute("data-id-name")}`
            ).value = amount;
          }
          //}
          input.value = amount;
        }
      }
      if (this.hasPart()) {
        this.total = this.length * amount;
        this.renderTotal();
      } else if (this.hasContribution()) {
        this.total = this.contribution;
        this.renderTotal();
      } else {
        const $length = amounts.length;
        if ($length > 0) {
          for (let i = 0; i < $length; i++) {
            amounts[i].addEventListener("change", function () {
              const value = parseInt(this.value, 10);
              //if (!amounts[i].getAttribute("disabled")) {
              self.addTotal(i, isNaN(value) ? null : value);
              //}
            });
          }
        }
      }
    }

    addTotal(index, value) {
      const lastValue = this.inputValues[index];
      if (value !== null) {
        this.total += value - lastValue;
        this.inputValues[index] = value;
        this.renderTotal();
      }
    }

    renderTotal() {
      document.querySelector(
        "#assistance_total"
      ).innerHTML = `<span> </span> ${this.total}`;
      let $count = this.indexes.filter((index) => !isEmpty(index));
      document.querySelector(
        "#assistance_total_contributors"
      ).innerHTML = `<span> </span> ${$count.length}`;
    }

    hasPart() {
      return this.is_amount === 1;
    }

    hasContribution() {
      return this.is_amount === 2;
    }

    hasAmount() {
      return (this.hasContribution() || this.hasPart()) === true;
    }

    addIndex(index) {
      this.indexes[index] = index;
      this.length++;
    }

    setIndex(index) {
      this.indexes.push(index);
      this.length++;
    }

    removeIndex(index) {
      delete this.indexes[index];
      this.length--;
    }
  }
  contrib = new ContributionManager();
  let part = parseInt(data.amount, 10);
  let contribution = parseInt(data.is_amount, 10);

  contrib.part = null;
  contrib.contribution = null;
  if (!isNaN(contribution)) {
    part = isNaN(part) ? 0 : part;
    if (contribution === 1) {
      contrib.part = part;
    } else {
      contrib.contribution = part;
    }
    contrib.is_amount = contribution;
  } else {
    contrib.is_amount = null;
  }
  //contrib.part = isNaN(part) ? null : part;
  //contrib.contribution = isNaN(contribution) ? null : contribution;

  let id = 0;
  for (const elt of amounts) {
    contrib.setIndex(id);
    contrib.inputValues.push(0);
    id++;
  }
  contrib.contributors = amounts;
  contrib.fillAmount(amounts);
  for (let i = 0; i < checkboxes.length; i++) {
    if (contrib.hasAmount()) {
      amounts[i].setAttribute("disabled", "disabled");
    }
    checkboxes[i].addEventListener("change", function () {
      let base_parent = this.parentNode.parentNode.parentNode.parentNode;

      if (this.checked) {
        contrib.addIndex(i);
        amounts[i].removeAttribute("disabled");
        base_parent.classList.remove("unchecked");
      } else {
        contrib.removeIndex(i);
        amounts[i].setAttribute("disabled", "disabled");
        amounts[i].value = 0;
        contrib.addTotal(i, 0);
        base_parent.classList.add("unchecked");
      }
      contrib.fillAmount([], true);
    });
  }
}
