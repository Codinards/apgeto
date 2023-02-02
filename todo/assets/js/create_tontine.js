class User {
  user = null;
  id = null;
  demi = null;
  count = null;

  /*old_count = null;
  old_user = null;
  old_id = null;
  old_demi = null;*/

  setUser(value) {
    if (!this.old_user) {
      this.user = value;
    }
  }

  setId(value) {
    if (!this.old_id) {
      this.id = value;
    }
  }

  setDemi(value) {
    this.demi = value;
  }

  setCount(value) {
    value = value == "null" ? 0 : value;
    this.count = parseInt(value, 10) + parseInt(this.old_count || 0, 10);
  }
  getUser() {
    return this.user;
  }

  getId() {
    return this.id;
  }

  getDemi() {
    return this.demi;
  }

  getCount() {
    return this.count;
  }
}

class Members {
  data = [];

  count = 0;

  addCount = true;

  labels = {
    name: null,
    halfName: null,
    andHalf: null,
  };

  members = {};

  olds = [];

  add(index, data) {
    index = parseInt(index, 10);
    if (!this.data[index]) {
      this.data[index] = data;
      this.count++;
    }
  }

  indexes = [];

  pushData(index, key, value, userId, message = false, adduser = false) {
    let lastIndex;
    index = parseInt(index, 10);
    if (message && this.hasUser(index, userId)) {
      if (message) {
        alert(message);
      }
    }
    if (adduser) {
      this.removeUser(index);
      this.addUser(value, index);
    }
    if (this.hasIndex(index)) {
      switch (key) {
        case "id":
          this.data[index].setId(value);
          break;
        case "count":
          this.data[index].setCount(value);
          break;
        case "demi":
          this.data[index].setDemi(value);
          //console.log(this.data);
          break;
        case "user":
          this.data[index].setUser(value);
          //console.log(this.members)
          //console.log(this.data)
          break;
      }
    }
  }

  get(index) {
    return this.data[index] || null;
  }

  remove(index) {
    index = parseInt(index, 10);
    if (this.get(index)) {
      delete this.data[index];
      this.removeUser(index);
      this.count--;
    }
  }

  hasIndex(index) {
    return (
      this.data[index] !== -1 &&
      this.data[index] !== undefined &&
      this.data[index] !== null
    );
  }

  addUser(user, index) {
    this.members[user] = index;
  }

  hasUser(index, value) {
    /*if(this.noNull(this.members[user])){
            if(this.members[user] !== value){
                return true 
            }
        }
        return false*/

    for (let i in this.data) {
      let element = this.data[i];
      i = parseInt(i, 10);
      if (element.getId() === value && index !== i) {
        return true;
      }
    }
    return false;
  }

  noNull(element) {
    return element !== -1 && element !== undefined && element != null;
  }

  getValue(index, key) {
    if (this.hasIndex(index) && key === "user") {
      let data = this.data[index];
      return data.getUser();
    }
    return null;
  }

  removeUser(index) {
    let user;
    for (user in this.members) {
      if (this.members[user] === index) {
        delete this.members[user];
      }
    }
  }

  addOld(index, user) {
    this.olds[index] = user;
  }

  setLabel(name, halfName, andHalf) {
    this.labels["name"] = name;
    this.labels["halfName"] = halfName;
    this.labels["andHalf"] = andHalf;
  }

  /*pushUsers(users) {
    if (this.addCount) {
      users.forEach((element, key) => {
        this.pushData(key, element);
      });
      this.addCount = false;
    } else {
      users.forEach((element, key) => {
        this.data[key] = element;
      });
    }
  }*/

  indexes = [];

  buildView(event = false) {
    //console.log(this.data);
    let olds = this.olds;
    /*let users = [];this.pushUsers(users);*/
    olds.forEach((element, key) => {
      if (this.data[key]) {
      } else {
        this.add(key, Object.assign(new User(), element));
        this.indexes[key] = element;
      }
    });

    let view = "";
    for (let element of this.data) {
      if (element !== undefined) {
        let count = parseInt(element.count, 10);
        if (this.olds[element.id]) {
          let exist = this.indexes[element.id];
          console.log(this.indexes);
          if (!exist || exist === -1) {
            // if (element.id === 6) {
            //   console.log(this.olds[element.id]);
            // }
            count = this.olds[element.id].count + count;
          } else {
            if (!event) delete this.indexes[element.id];
          }
          console.log(this.indexes);
        }
        let si = count == "0" || count == null ? false : true;
        view += `<tr>
                    <td>${element.getUser()}</td>
                    <td>${
                      element.getDemi() && si
                        ? count +
                          " " +
                          this.labels["name"] +
                          (count > 1 ? "s" : "") +
                          " " +
                          this.labels["andHalf"]
                        : element.getDemi()
                        ? this.labels["halfName"]
                        : si
                        ? count +
                          " " +
                          this.labels["name"] +
                          (count > 1 ? "s" : "")
                        : ""
                    }</td>
                </tr>`;
      }
    }
    //this.indexes = [];
    return view;
  }

  renderVieW(element, allCount, event = false) {
    element.innerHTML = this.buildView(event);
    allCount.innerHTML = this.count;
  }
}

//let inputs = $('input.selector-input')
let tontineurs = new Members();
let data_info_div = document.querySelector("#react_app_o");
if (data_info_div) {
  tontineurs.setLabel(
    data_info_div.getAttribute("data-name"),
    data_info_div.getAttribute("data-halfname"),
    data_info_div.getAttribute("data-andhalf")
  );
  let table = document.querySelector("#tontineur-count-render");
  let allCount = document.querySelector("#member_numbers");

  window.addEventListener("load", function () {
    let tontine_data_info = document.querySelector("#react_app");
    /*const selects = document.querySelectorAll("select.user-item-field");
  for (const element of selects) {
    const options = element.querySelectorAll("option:not([selected])");
    for (const option of options) {
      element.removeChild(option);
    }
  }*/
    if (tontine_data_info) {
      let donnees = JSON.parse(tontine_data_info.getAttribute("data-info"));
      let cle;
      for (let key in donnees) {
        $item = donnees[key];
        cle = parseInt($item.id, 10);
        $user = new User();
        $user.count = parseInt($item.count, 10);
        $user.demi = $item.demi;
        $user.id = cle;
        $user.user = $item.user;

        tontineurs.addOld(cle, $user);
        /*tontineurs.pushData(cle, "id", cle);

      tontineurs.pushData(, "user", , cle, false, true);
      tontineurs.pushData(cle, "id", cle);
      tontineurs.pushData(cle, "count", );
      tontineurs.pushData(cle, "demi", );*/
      }
      //console.log(tontineurs);
    }
    //tontineurs.renderVieW(table, allCount);
    let selectors = document.querySelectorAll("input.selector-input");
    selectors.forEach(function (element, index) {
      manageSelector(element, tontineurs, table, allCount);
      element.addEventListener("change", function () {
        manageSelector(element, tontineurs, table, allCount);
        tontineurs.renderVieW(table, allCount, true);
      });
    });
    tontineurs.renderVieW(table, allCount);
  });

  function insertSelectField(isCount, tontineurs, elt, id) {
    option = elt.options[elt.selectedIndex];

    if (isCount !== -1) {
      let userIdIs = parseInt(option.value, 10);
      tontineurs.pushData(
        id,
        "user",
        option.innerHTML,
        userIdIs,
        `Vous Avez deja selectionner le membre "${option.innerHTML}". Dans ce cas seul le premier choix sera pris au compte`,
        true
      );
      tontineurs.pushData(id, "id", userIdIs);
    } else {
      tontineurs.pushData(id, "count", option.value);
    }
  }

  function manageSelector(element, tontineurs, table, allCount) {
    let classAttr = element.getAttribute("data_class");
    let id = element.getAttribute("data_id");
    let brothers = document.querySelectorAll(`.${classAttr}`);
    if (element.checked) {
      tontineurs.add(id, new User());
      brothers.forEach(function (elt, key) {
        if (elt.getAttribute("type")) {
          // si les elements sont checked a l,affichage de la page
          tontineurs.pushData(id, "demi", false);
          if (elt.checked) {
            tontineurs.pushData(id, "demi", true);
            // tontineurs.renderVieW(table, allCount);
          } else {
            if (elt.getAttribute("type") === "hidden") {
              tontineurs.pushData(id, "user", elt.value);
              // tontineurs.renderVieW(table, allCount);
            } else {
              tontineurs.pushData(id, "demi", false);
              // tontineurs.renderVieW(table, allCount);
            }
            tontineurs.pushData(id, "demi", false);
            // tontineurs.renderVieW(table, allCount);
          }
          // apres chaque changement
          elt.addEventListener("change", function () {
            if (elt.checked) {
              tontineurs.pushData(id, "demi", true);
              //tontineurs.renderVieW(table, allCount);
            } else {
              tontineurs.pushData(id, "demi", false);
              //tontineurs.renderVieW(table, allCount);
            }
          });
        } else {
          let name = elt.getAttribute("id");
          let isCount = name.indexOf("user");
          insertSelectField(isCount, tontineurs, elt, id);
          //tontineurs.renderVieW(table, allCount);
          elt.addEventListener("change", function () {
            insertSelectField(isCount, tontineurs, elt, id);
            //tontineurs.renderVieW(table, allCount);
          });
        }
      });
    } else {
      if (tontineurs.get(id)) {
        tontineurs.remove(id);
        //tontineurs.renderVieW(table, allCount);
      }
    }
  }
}
