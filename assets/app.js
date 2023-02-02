import React from "react";
import { render } from "react-dom";
import Assistance from "./components/forms/assistances/assistance";
import Tontiners from "./components/forms/tontines/tontiners";
import Detail from "./components/forms/tontines/detail";
import Functionalities from "./components/functionalities/functionalities";
import translator, { devKeys } from "./utils/functions";
require("./styles/app.css");

const app = document.getElementById("react_app");
if (app) {
  const appType = app.getAttribute("app-type");
  if (appType === "assistance") {
    let firstStore = { min: null, max: null };
    const filterForm = document.querySelector("#filter_assistance_form");
    const minInput = document.querySelector("#minCashBalance");
    const maxInput = document.querySelector("#maxCashBalance");
    const totalContributions = document.querySelector(
      "#assistance_total_contribution"
    );
    const totalContributors = document.querySelector(
      "#assistance_total_contributors"
    );

    const setTotalData = function (amount, number) {
      totalContributions.innerHTML = amount;
      totalContributors.innerHTML = number;
    };

    filterForm.addEventListener("submit", (e) => {
      e.preventDefault();
    });

    render(
      <Assistance
        maxInput={maxInput}
        minInput={minInput}
        firstStore={firstStore}
        setTotalData={setTotalData}
      />,
      app
    );
  } else if (appType == "tontine") {
    const detail = document.querySelector("#tontineur-count-render");
    const labels = JSON.parse(detail.getAttribute("total-data"));
    const data = JSON.parse(app.getAttribute("data-tontines"));

    render(<Tontiners propData={data} />, app);
    render(<Detail labels={labels} propData={data} />, detail);
  } else if (appType == "functionalities") {
    const data = JSON.parse(app.getAttribute("data-info"));
    app.removeAttribute("data-info");
    devKeys.keys = JSON.parse(app.getAttribute("dev-keys"));
    render(<Functionalities firstData={data} />, app);
  }
}
