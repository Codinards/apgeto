import React, { useEffect, useState } from 'react';
import Ended from './modules/ended';
import Ongoing from './modules/ongoing';
import Waiting from './modules/waiting';

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
                { index == 0 && <Ended  data={data[module_name]} module={module_name}  handleData={updateData} />}
                    { index == 1 && <Ongoing data={data[module_name]} module={module_name} handleData={updateData} />}
                    { index == 2 && <Waiting data={data[module_name]} module={module_name} handleData={updateData} />}
                </div>
            </div>)
            })
            setView([...newview]);
            
    }, [data])
    

    const updateData = async (id, get_param , complement)  => {
        let attrs = id.split('_')
        let name = attrs[0];
        let mod = attrs[1];
        let sub = attrs[2];
        let index = attrs[3]
  

        let response = await fetch('/functionalities-update?index=' + get_param, {
            method: 'GET',
        })
        let returned = await response.json();
        if(response.ok){
            returned = JSON.parse(returned)
            if(returned.success){
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
        }else{
            console.error(response)
        }
    }}

    return (
<>
    {view}
</>

        
    )       
    /*<div className="card-body bg-save">
            
        </div>*/
}

export default Functionalities