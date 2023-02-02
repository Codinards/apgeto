import Translator from './translator'

export const isEmpty = (value) => {
  return (
    value === undefined ||
    value === null ||
    (typeof value === "object" && Object.keys(value).length === 0) ||
    (typeof value === "string" && value.trim().length === 0)
  );
};

export const componentKeys = {
  count: 0,

  getcount() {
    this.count++;
    return this.count;
  },
};

export const devKeys = { keys: {} };

export const globalData = {
  data: {},

  map(data, callback) {
    let mapped = [];
    for (const key in data) {
      mapped.push(callback(data[key], key));
    }
    return mapped;
  },

  findIndex(data, index){
    for (const key in data) {
      if(index == data[key]) return key;
    }
    return undefined;
  }
};

export const catalogueData = {
  translations: {},

  setData(key, data) {
    this.translations[key] = data;
  },

  getData(key) {
    return this.translations[key] || null;
  },

  setTranslationCatalogue(data) {
    this.translator = data;
  },
}

/**------------- Transmission of translation data ------------*/
const catalogueScript = document.getElementById("catalogue_data");
const translator = Translator(catalogueScript.dataset.locale);

translator.setCatalogues(JSON.parse(catalogueScript.dataset.translationData));

catalogueScript.removeAttribute("data-translation-data");
/** -------------------------------------------------- */

export default translator;
