import { isEmpty } from "./functions";

const Translator = function (currentDomain) {
  let translator = {
    catalogues: {},
    currentDomain,

    setCatalogues(catalogues) {
      this.catalogues = catalogues;
      return this;
    },

    trans(key, attrs = {}, domain = null) {
      let usedDomain = domain || this.currentDomain;
      if (!this.catalogues[usedDomain]) return null;
      if (!this.catalogues[usedDomain][key]) return null;
      let $translation = this.catalogues[usedDomain][key];
      if (isEmpty(attrs)) return $translation;
      for (const k in attrs) {
        $translation = $translation.replace(`{{ ${k} }}`, attrs[k]);
        //$translation = $translation.replace(`{{${k}}}`, attrs[k]);
        $translation = $translation.replace(`%${k}%`, attrs[k]);
      }

      return $translation;
    },

    setCurrentDomain(currentDomain) {
      this.currentDomain = currentDomain;
      return this;
    },
  };
  let instance = translator;
  return instance.setCurrentDomain(currentDomain);
};

export default Translator;
