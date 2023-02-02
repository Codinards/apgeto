import React, {useEffect, useState} from 'react'

const Filter = ({minCallback, maxCallback}) => {
    const [maxValue, setMaxValue] = useState(null) 
    const [minValue, setMinValue] = useState(null)
    const [ismounted, setIsmounted] = useState(false)
    const [form, setForm] = useState('')

    useEffect(() => {
      if(!ismounted){
        setForm(form);
        setIsmounted(true)
      }
    })


      return (
      <>
        <div className="form-group">
          <label style={{ fontWeight: "bold" }} htmlFor="minCashBalance" >Mincashbalance</label>
          <input 
          type="number" 
          id="minCashBalance" 
          name="minCashBalance" 
          className="form-control"
          defaultValue=''
          onChange={minCallback}/>
        </div>
        <div className="form-group">
          <label style={{ fontWeight: "bold" }} htmlFor="maxCashBalance">Maxcashbalance</label>
          <input 
          type="number" 
          id="maxCashBalance" 
          name="maxCashBalance" 
          className="form-control"
          defaultValue=""
          onChange={maxCallback}/>
        </div>
    </>
    )
  }

  export default Filter;