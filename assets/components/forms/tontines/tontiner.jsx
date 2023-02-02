import React, { useEffect, useState } from "react";

const Tontiner = ({id, key_prop, data, member, handleChecked, handleCount, handleHalfName}) => {
    const [maxUnityCount, setMaxUntityCount] = useState(10);
    const [tontinerRow, setTontinerRow] = useState('');
    const [isMounted, setIsMounted] = useState(false);
    const [toggleCheck, setToggleCheck] = useState(false);

     useEffect(() => {
        if(!isMounted){
            let options = [];
            let isSelected = false;
        for (let index = 0; index < maxUnityCount; index++) {
            options.push(<option value={index+1} key={index} >{index+1} {index > 0 ? data.unities : data.unity}</option>)
            isSelected = false;
        }
       
        options.push(<option value="0.5" key={0.5}>{data.none}</option>)

        const view = ( 
        <div className={"row  m-2 bg-success"} id="parent-1">
            <div className="col-6">
                <div className="form-group">
                    <div className="form-check">        
                    <input 
                        defaultChecked={member.checked}
                        onChange={(e) => {
                            handleChecked(e, key_prop)
                        }}
                        type="checkbox" 
                        id={"tontine_tontineurData_" + key_prop + "_isSelected"} 
                        name={"tontine[tontineurData][" + key_prop + "][isSelected]"} 
                        className={"form-group-item-" + id + "selector-input form-check-input"}
                        data_id={id} 
                        value='1'
                    />
                        <label style={{ display: "inline-block", fontSize: "1.2em", fontWeight: "bold" }} className="text-edit form-check-label" htmlFor={"tontine_tontineurData_" + key_prop + "_select"} >
                            {member.name}
                        </label>
                    </div>
                </div>
            </div>
            <div className="col-3 form-group-item">
                <div className="form-group">
                    <label className="text-show required" style={{ fontWeight: "bold" }} htmlFor={"tontine_tontineurData_" + key_prop + "_count"} >
                        {data.count_label}
                    </label>
                <select id={"tontine_tontineurData_" + key_prop + "_count"} name={"tontine[tontineurData][" + key_prop + "][count]"} className={"form-group-item-" + id +" form-control"} style={{ color: "#b88517" }} onChange={(e) => {handleCount(e, key_prop)}} defaultValue={1}>
                    {options.map((option) => option)}
                        
                </select>
                </div>
            </div>
            <div className="col-3 form-group-item">
                <div className="form-group">
                    <div className="form-check">
                        <input 
                        defaultChecked={ false}
                            type="checkbox" 
                            id={"tontine_tontineurData_" + key_prop + "_demiNom"} 
                            name={"tontine[tontineurData][" + key_prop + "][demiNom]"} 
                            className={"form-group-item-" + id + " form-check-input"} 
                            value="1"
                            onChange={(e) => handleHalfName(e, key_prop)}
                        />
                        <label className="text-edit form-check-label" style={{ fontWeight:"bold" }} htmlFor={"tontine_tontineurData_" + key_prop + "_demiNom"}>
                            {data.half_check_label}
                        </label>
                    </div>
                </div>
            </div>
            
                <input 
                    type="hidden" 
                    id={"tontine_tontineurData_" + key_prop + "_name"} 
                    name={"tontine[tontineurData][" + key_prop + "][name]"} 
                    className={"user-item-field" }
                    value={member.name}
                />

            
        </div>);
//<div className="col-1 form-group-item"></div>
        setTontinerRow(view);
    }
    setIsMounted(true)
        }, [toggleCheck])

        useEffect(() => {
            setToggleCheck(member.checked)
        }, [isMounted])

        
    return (
        <>{tontinerRow}</>  
    );
}

export default Tontiner;