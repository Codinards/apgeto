import React , {useEffect, useLayoutEffect, useMemo, useRef, useState} from 'react'
import Totals from './totals'
import reactDom from 'react-dom'
import Filter from './filter'

const assistance = function({maxInput, minInput, firstStore, setTotalData}) {
  
  //{minBalance, maxBalance}
    const [amount, setAmount] = useState(0)
    const [ismounted, setIsmounted] = useState(false)
    const [contributors, setContributors] = useState([])
    const [countContributors, setCountContributors] = useState(0)
    const [ contributionData, setContributionData] = useState({})
    const [ totalAmount, setTotalAmount] = useState(0)
    const [view, setView] = useState([])
    //const [selectLabel, setSelectLabel] = useState('Select All')
    const [minBalance, setMinbalance] = useState(null)
    const [maxBalance, setMaxbalance] = useState(null)
    const [store, setStore] = useState({min: null, max: null})
    const [callcount, setCallcount] = useState(0)
    
    


  useEffect(() => {
    maxInput.addEventListener('change', (e) => {
      const value = parseInt(e.target.value, 10);
      const newStore = {}
      newStore.min = store.min
      if(!isNaN(value) /*&& (store.min === null || (store.min && value > store.min))*/){
            newStore.max = value
      }else{
        newStore.max = null
      }
      firstStore.max = newStore.max
      setStore(newStore)

      /*const newContributors = contributors.map((contributor) => {
        if(firstStore.min && firstStore.max){
           if(firstStore.min <= contributor.cashBalances && firstStore.max >= contributor.cashBalances)
            contributor.checked = true
           else contributor.checked = false
        }
        else if(firstStore.min){
          if(firstStore.min <= contributor.cashBalances) contributor.checked = true
           else contributor.checked = false
        }else if(firstStore.max){
          if(firstStore.max >= contributor.cashBalances) contributor.checked = true
          else contributor.checked = false
        }
        return contributor
      })

      setContributors(newContributors);*/
    })
  
    minInput.addEventListener('change', (e) => {
      const value = parseInt(e.target.value, 10);
      const newStore = {}
      newStore.max = store.max
      if(!isNaN(value)){
        newStore.min = value
      }else{
        newStore.min = null
      }
      firstStore.min = newStore.min
      setStore(newStore)
    })
  }, [])


    const isEmpty = (value) => {
        return (
          value === undefined ||
          value === null ||
          (typeof value === "object" && Object.keys(value).length === 0) ||
          (typeof value === "string" && value.trim().length === 0)
        );
      };

    const sum = (arrayOfInt) => {
      let result = 0;
      for (const unity of arrayOfInt) {
        result += unity;
      }
      return result;
    }

      useEffect(function(){
        if(!ismounted){
          const form = document.getElementById('react_app')
          //const root = form.querySelector('#react_app')
          const user_data = JSON.parse(form.getAttribute('data-users')) 
          const baseData = JSON.parse(form.getAttribute('data-assistance-info'))
          let lastUser;
        setContributionData(baseData)
        const contributorBuilding = [];
        let i = 0;
          for (const user of user_data) {
            user.index = parseInt(user.index, 10)
            user.checked = baseData.is_amount == '3'? false : true;
            user.amount =  amount;
            user.etat = true
            user.amount_label = baseData.amount_label;
            user.balance_label = baseData.balance_label
            user.member_label = baseData.member_label
            user.devise = baseData.devise
            user.is_amount = baseData.is_amount
            user.cashBalances = (() => { 
              const balance = parseInt(user.cashBalances, 10); 
              return (!isNaN(balance) ? balance : 0)
            })()
            
            contributorBuilding[user.index] = user
            setContributors(contributorBuilding)
            lastUser = user;
            i++;
          }
          setIsmounted(true)
          form.removeAttribute('data-users')
          form.removeAttribute('data-assistance-info')
        }
        
        if(countContributors == 0){
          setCountContributors(1)
        }else if(countContributors !== contributors.length){
          setCountContributors(contributors.length)
        }
      }, [countContributors])

      const roudAmount =  (amount) => {
        let relicat = 0;
        let lastMontantChar = (amount + "").substring((amount + '').length - 2);
        if(lastMontantChar <= 50 ){
          relicat = 50 - parseInt(lastMontantChar, 10);
        }else{
          relicat = 100 - parseInt(lastMontantChar, 10);
        }
        return amount + relicat;
      }

      // Managing amount
      useEffect(() => {
        
          const contributorsLength = filterContributors(contributors).length
              if(contributionData.is_amount === "1"){
                setAmount(contributionData.amount)
                setTotalAmount(parseInt(contributionData.amount, 10) * contributorsLength)
              } 
              if(contributionData.is_amount === "2"){
                const montant = roudAmount(parseInt(contributionData.amount/contributorsLength, 10) + 1);
                setAmount(montant)
                setTotalAmount(contributionData.amount)
              }
              if(contributionData.is_amount === '3'){
                setTotalAmount(
                  sum(
                    filterContributors(contributors)
                    .map((contributor) => contributor.amount))
                )
              }
              
            }, [countContributors, contributors, store])


            const filterContributors = function(data){ 
              return data.filter((contributor) => {
                /*if(isEmpty(contributor)){
                  return false;
                }*/
              let isChecked = contributor.checked
              if(firstStore.min && firstStore.max){
                return firstStore.min <= contributor.cashBalances && firstStore.max >= contributor.cashBalances && isChecked
              }
              else if(firstStore.min){
                return firstStore.min <= contributor.cashBalances && isChecked
              }else if(firstStore.max){
                return firstStore.max >= contributor.cashBalances && isChecked
              }
              return isChecked
            })
          }

            // Managing input check
            const changeChecked = (e) => {
              
              const index = e.target.getAttribute('data_index')
              contributors[index].checked =  !contributors[index].checked
              setContributors([...contributors])
              const contributorsLength = filterContributors(contributors)
              if(contributionData.is_amount === '1'){
                setTotalAmount(contributorsLength.length * contributionData.amount)
              }else if(contributionData.is_amount === '2'){
                let montant = roudAmount(parseInt(contributionData.amount/contributorsLength.length, 10) + 1)
                // setAmount(montant)
                setAmount(montant)
              }else{
                setTotalAmount(
                  sum(
                    filterContributors(contributors)
                    .map((contributor) => contributor.amount))
                )
              }

            }
            // End managing input check
            const changeValue = (e) =>{
              const elt = e.target;
              const index = elt.getAttribute('data_index');
              const value = !isEmpty(elt.value) ? elt.value : 0;
              contributors[index].amount = parseInt(value,10)
              setContributors([...contributors])
              setTotalAmount(
                sum(
                  (filterContributors(contributors))
                  .map((contributor) => contributor.amount))
              )
            }

      const builView = () => {
    
        setTotalData(totalAmount, filterContributors(contributors).length);

        let builberView = contributors.filter(function(contributor){
          if(isEmpty(contributor)){
            return false;
          }
          let cashBalance = parseInt(contributor.cashBalances, 10)
          
          if(firstStore.max && firstStore.min == null){
            return cashBalance <= firstStore.max
          }else if(firstStore.min && firstStore.max == null){
            return cashBalance >= firstStore.min
          }else if(firstStore.max && firstStore.min){
            return cashBalance >= firstStore.min && cashBalance <= firstStore.max
          }
          return true;
        }).map(function(contributor, index) {
          return (contributor.etat ? (<><div className={"row border-top" + (contributor.checked === true ? '' : ' bg-grey')} key={index} style={{"margin": "0px", "padding": "0px"}}>
          <div className="col-md-4">
              <div className="form-group">
                  <div className="form-check">
                      <input 
                      type="checkbox" 
                      id= {'assistance_contributors_' + contributor.index + '_select'} 
                      name={"assistance[contributors][" + contributor.index + "][select]"} 
                      defaultChecked={contributor.checked} 
                      data_index={ contributor.index } 
                      className="form-check-input" 
                      defaultValue={contributor.checked ? 1 : 0} 
                      onChange={() => contributor.checked ? 1 : 0} 
                      onClick={changeChecked}
                      />
                      <label 
                      style={{ fontWeight: 500 }} 
                      className="form-check-label"  
                      htmlFor={"assistance_contributors_" + contributor.index + "_select"}
                      >
                          {contributor.name}
                      </label>
                  </div>
              </div>
          </div>
          <div className="col-md-4">
              <div className="form-group">
                  <label 
                  style={{ fontWeight: 500 }} 
                  htmlFor={"assistance_contributors_" + contributor.index + "_amount"} 
                  className="required"
                  >
                      {contributor.amount_label}
                  </label>
                  {
                  contributor.is_amount === '3' 
                  
                  ?
                      <input 
                      type="number" 
                      id={"assistance_contributors_" + contributor.index + "_amount"} 
                      name={"assistance[contributors][" + contributor.index +"][amount]"}
                      required="required" 
                      className={"contributor_amount_input form-control " + (contributor.checked == false && 'bg-grey')}
                      defaultValue={contributor.checked && contributor.amount > 0 ? contributor.amount : 0 }
                      onChange={changeValue}
                      data_index={contributor.index}
                      disabled={contributor.is_amount !== '3' ? !contributor.checked : (contributor.checked ? false : true)}
                      required={contributor.checked}
                      />
                   : 
                      <input 
                      type="number" 
                      id={"assistance_contributors_" + contributor.index + "_amount"} 
                      name={"assistance[contributors][" + contributor.index +"][amount]"}
                      required="required" 
                      className={"contributor_amount_input form-control " + (contributor.checked == false && 'bg-grey')}
                      value={contributor.checked ? amount : 0 }
                      //value={contributor.is_amount === '3' ? (contributor.checked && contributor.amount > 0 ? contributor.amount : 0) : (contributor.checked ? amount : 0) }
                      //value={ !contributor.checked ? 0 : (contributor.is_amount === '3' ? contributor.amount : amount)
                      onChange={changeValue}
                      data_index={contributor.index}
                      disabled={contributor.is_amount !== '3' ? !contributor.checked : (contributor.checked ? false : true)}
                      required={contributor.checked}
                      />
                   }
                  
              </div>
          </div>
          <div className="col-md-4">
              <div className="bg-update mt-2" >
                <table className="table text-white">
                  <tbody>
                    <tr>
                      <th>{contributor.balance_label} </th>
                      <td>{contributor.cashBalances} {contributor.devise}</td>
                    </tr>
                  </tbody>
                </table>
              </div>  
          </div>
          <input type="hidden" id={'assistance_contributors_' + contributor.index+ '_index'} name={"assistance[contributors][" + contributor.index + "][index]"} value={contributor.index}/>
      </div></>): ''
)
        })
        setView(builberView);
      }

      // View construction
      useEffect(function(){
        setCallcount(callcount + 1)
        builView()
        
      }, [countContributors, contributors, store, totalAmount, amount])
      // end view contruction

      return <>
        <>{view.map((elt) => elt)}<hr/></>
      </>    
}

export default assistance;