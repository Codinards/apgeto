import React, { useEffect, useState } from 'react';
import { devKeys, globalData } from '../../utils/functions';
import BaseModule from './baseModule';

const isEmpty = (value) => {
    return (
      value === undefined ||
      value === null ||
      (typeof value === "object" && Object.keys(value).length === 0) ||
      (typeof value === "string" && value.trim().length === 0)
    );
};

const Functionalities = ({firstData}) => {
    const [keys, setKeys] = useState([]);
    const [mounted, setMounted] = useState(false);
    const [data, setData] = useState({})
    const [view, setView] = useState([])
    const [localData, setLocalData] = useState({
        key: "",
        subKey: "",
        action: ""
    })

    const eventData = {data:{}}

    useEffect(() =>{
        if(!mounted){
            let newKeys = [];
            for (const id in firstData) {
                newKeys.push(id);
            }
            
        setKeys(newKeys);
        setMounted(true);
        setData(firstData);
    }else{
        let newKeys = [];
        for (const id in data) {
            newKeys.push(id);
        }
        setKeys(newKeys);
    }
    }, [mounted, data])

    useEffect(() =>{
        const newview = keys.map(function(module_name, index) {
            
            return (<div key={index} className="card bg-edit mb-3">
                <div className="my-modal-header card-header">
                    <h2 className="modal-title">{module_name}</h2>
                </div>
                <div className="card-body bg-save">
                    { index == 0 && <BaseModule  data={data} module={module_name}  handleData={updateData} complement_module='ongoing' />}
                    { index == 1 && <BaseModule data={data} module={module_name} handleData={updateData} complement_module='ended' />}
                    { index == 2 && <BaseModule data={data} module={module_name} handleData={updateData} complement_module='ongoing' />}
                    
                </div><div className='bg-secondary'><hr/></div>
            </div>)
            })
            setView([...newview]);
            
    }, [data])

    const update = async (e) => {
    e.preventDefault();
    const element = e.target

setLocalData(()=>{
        return {key: element.querySelector('#category').value,
        subKey: element.querySelector('#sub_category').value,
        action: element.querySelector('#action').value}
    })

    if(localData.key.trim() != '' && localData.subKey.trim() != '' && localData.action.trim() != ''){
        let keys = devKeys.keys.module
        
        if(keys.find(element => element == globalData.findIndex(devKeys.keys.trans, localData.key) )){
            keys = devKeys.keys.submod
            if(keys.find(element => element == globalData.findIndex(devKeys.keys.trans, localData.subKey))){
                setLocalData(()=>{
                    return {
                        key: element.querySelector('#category').value,
                        subKey: element.querySelector('#sub_category').value,
                        action: element.querySelector('#action').value,
                        module: 'waiting'
                    }
                })
                // localData = {, ...localData}
                let response = await fetch('/functionalities-new?module=' + localData.key + '&submodule=' + localData.subKey + '&action=' + localData.action, {
                    method: 'GET',
                })
                let returned = await response.json();
                if(response.ok){
                    returned = JSON.parse(returned)
                    const baseMod = globalData.findIndex(devKeys.keys.trans, 'waiting');
                    const localKey = globalData.findIndex(devKeys.keys.trans, localData.key);
                    const localSubkey = globalData.findIndex(devKeys.keys.trans, localData.subKey);
                   
                    if(returned.success){
                        if(data[baseMod][localKey] == undefined){
                            data[baseMod][localKey] = {};
                        }
                        if(data[baseMod][localKey][localSubkey] == undefined){
                            data[baseMod][localKey][localSubkey] = [];
                        }
                        data[baseMod][localKey][localSubkey].push(localData.action)
                        setData({...data})
                        alert("l'enregistrement s'est bien passé");
                        setLocalData(() => {localData = {action:"", ...localData}})
                    }else{
                        console.error(returned);
                    }
                }else{
                    console.error(response);
                }
            }
        }
    }
    }

    
const findIndex = (index) => {
    return globalData.findIndex(devKeys.keys.trans, index);
}

const trans = (index) => {
    return devKeys.keys.trans[index];
}
    const updateData = async (id, get_param , complement)  => {

        let attrs, name, mod, sub, index;
        if(id){
            attrs = id.split('_') 
            name = attrs[0];
            mod = attrs[1];
            sub = attrs[2];
            index = attrs[3]
        }else{
            attrs = get_param.split('_') 
            name = findIndex(attrs[0]);
            mod = findIndex(attrs[1]);
            sub = findIndex(attrs[2]);
            index = attrs[3]
        }
        
  
        let response = await fetch('/functionalities-update?index=' + get_param, {
            method: 'GET',
        })
        let returned = await response.json();
        if(response.ok){
            returned = JSON.parse(returned)
            if(returned.success){
            complement = findIndex(complement)
            if(data[complement][mod] == undefined){
                data[complement][mod] = {};
            }
            if(data[complement][mod][sub] == undefined){
                data[complement][mod][sub] = [];
            }
            
            data[complement][mod][sub].push(data[name][mod][sub][index]);
            let newIndex = [];
            let i = 0;
            for (const ind in data[name][mod][sub]) {
                if(parseInt(index, 10)  != parseInt(ind, 10)){
                    newIndex[i] = data[name][mod][sub][ind]
                }
                i++;
            }
            if(!isEmpty(newIndex))  data[name][mod][sub] = newIndex;
            else {
                delete data[name][mod][sub]
                if(isEmpty(data[name][mod])) delete data[name][mod];
            };
            
            setData({...data});
            eventData.data = {...data}
        }else{
            console.error(response)
        }
    }}

    return (
<div className='row'>
    <div className={"col-md-8 " + (devKeys.keys.env == 'dev' ? '' : ' mx-auto')}>{view}</div>
    { devKeys.keys.env == 'dev' && (<div className="col-md-4">
            <div className="card">
                <div className="card-header">
                    <h2 className="card-title">add functionnality</h2>
                </div>
                <div className="card-body">
                    <form action="" id="add-action-form" onSubmit={(e) => update(e)}>
                        <div className="form-group">
                            <label htmlFor="category">category</label>
                            <select name="category" id="category" className="form-control" >
                                <option value=""></option>
                                {globalData.map(devKeys.keys.module, function(elt, index){
                                    return <option key={index} value={ trans(elt) }>{elt}</option>
                                })}
                            </select>
                        </div>
                        <div className="form-group">
                            <label htmlFor="sub_category">sub_category</label>
                            <select type="text" name="sub_category" id="sub_category" className="form-control" >
                            <option value=""></option>
                            {globalData.map(devKeys.keys.submod, function(elt, index){
                                    return <option key={index} value={ trans(elt) }>{elt}</option>
                                })}
                            </select>
                        </div>
                        <div className="form-group">
                            <label htmlFor="action">action</label>
                            <input 
                                type="text"
                                name="action"
                                id="action"
                                className="form-control"
                                value={localData.action}
                                onChange={(data)=> setLocalData({...localData, action: data})}
                            />
                        </div>
                        <div className="form-group">
                            <button className="btn btn-primary">ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>)}
    
</div>

        
    )       
    /*<div className="card-body bg-save">
            
        </div>*/
}

export default Functionalities