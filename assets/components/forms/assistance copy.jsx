import React , {useEffect, useLayoutEffect, useState} from 'react'
//import { amountContext } from '../contexts';
import FormRow from '../olds/form_row';
//import {isEmpty } from '../../utils/functions.jsx'



const assistance = () => {
  
    const [contributors, setContributors] = useState([])
    const [amount, setAmount] = useState(0)
    const [ismounted, setIsmounted] = useState(false)
    const [viewContributors, setViewContributors] = useState([])
    const [countContributors, setCountContributors] = useState(0)
    const [contributionData, setContributionData] = useState({})

    const isEmpty = (value) => {
        return (
          value === undefined ||
          value === null ||
          (typeof value === "object" && Object.keys(value).length === 0) ||
          (typeof value === "string" && value.trim().length === 0)
        );
      };

      useEffect(function(){
        if(!ismounted){
          const form = document.getElementById('assistance_form')
          const root = form.querySelector('#react_app')
          setContributors(JSON.parse(root.getAttribute('data-users')) )
          setContributionData(JSON.parse(root.getAttribute('data-assistance-info')))
          setIsmounted(true)
        }
        if(countContributors == 0){
          setCountContributors(1)
        }else if(countContributors !== contributors.length){
          setCountContributors(contributors.length)
        }
        
      }, [countContributors])

      useEffect(() => {
              if(contributionData.is_amount === "0"){
                setAmount(parseInt(contributionData.amount/countContributors, 10) + 1)
              }
              if(contributionData.is_amount === "1"){
                setAmount(contributionData.amount)
              }
            }, [countContributors])


         useEffect(function(){
        contributors.forEach((elt, index) => {
          if(isEmpty(viewContributors[index])){
            const {devise, amount_label, balance_label} = contributionData
            elt = {...elt, devise, amount_label, balance_label}
            viewContributors[index] = <FormRow data={elt} key={index} index={index} callback={handleContributors} etat={true} handleAmount={amount} />;
          }
        })
        viewContributors = [...viewContributors]
        setViewContributors(viewContributors)
        }, [countContributors, amount])


      const handleContributors = (index, checked) => {
        let newContributors = contributors
        index += 1
        if(!checked){
          if(!isEmpty(contributors[index])){
            const remove = contributors[index]
            newContributors = contributors.filter((elt) => {

              elt !== remove
            })
          }
        setContributors(newContributors)
        }   
      }

    //return <amountContext.provider value={{ amount: amount }}>
      return <div>
        {viewContributors.map((elt) => elt)}
      </div>
    //</amountContext.provider>
      
        
}//
/*
/*{assistance_form.children.map((child) => {
                <FormRow/>
            })}*/
export default assistance;