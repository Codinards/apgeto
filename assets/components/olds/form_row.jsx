import React, {useContext, useEffect, useState} from 'react';
//import { amountContext } from '../contexts';


function FormRow (props) {
    //const valeur = useContext(amountContext)
    const [checked, setChecked] = useState(true)
    const [value, setValue] = useState(0);
    const [etat, setEtat] = useState(true);
    const [amount, setAmount] = useState(0);

    //render() {
        const {data, index, handleAmount} = props
        //const data = props.data
        //const index = props.index

        const changeChecked = (e) => {
            setChecked(!checked)
            setEtat(!etat)
            console.log(data)
            return checked
        }


        return ( etat == true ?

            <div className={"row " + (!checked && 'bg-grey')}>
                <div className="col-md-4">
                    <div className="form-group">
                        <div className="form-check">
                            <input 
                            type="checkbox" 
                            id= {'assistance_contributors_' + index + '_select'} 
                            name={"assistance[contributors][" + index + "][select]"} 
                            defaultChecked={changeChecked} data-index={ index } 
                            className="form-check-input" 
                            defaultValue={1} onChange={() => 1} 
                            onClick={changeChecked}
                            onChange={() => 1}
                            />
                            <label 
                            style={{ fontWeight: 500 }} 
                            className="form-check-label"  
                            htmlFor={"assistance_contributors_" + index + "_select"}
                            >
                                {data.name}
                            </label>
                        </div>
                    </div>
                </div>
                <div className="col-md-4">
                    <div className="form-group">
                        <label 
                        style={{ fontWeight: 500 }} 
                        htmlFor={"assistance_contributors_" + index + "_amount"} 
                        className="required"
                        >
                            {data.amount_label}
                        </label>
                        <input 
                        type="number" 
                        id={"assistance_contributors_" + index + "_amount"} 
                        name={"assistance[contributors][" + index +"][amount]"}
                         required="required" 
                         className={"contributor_amount_input form-control " + (checked == false && 'bg-grey')} 
                         defaultValue={amount} onChange={(e) => e.value }
                         disabled={!checked}
                         required={checked}
                         />
                    </div>
                </div>
                <div className="col-md-4">
                    <div className="bg-update mt-2">{data.balance_label} : {data.cashBalances} {data.devise}</div>  
                </div>
            </div> : ''
        )
    //}
}

export default FormRow;