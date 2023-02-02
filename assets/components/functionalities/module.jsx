import React from 'react';
import { globalData } from '../../utils/functions';
import Element from './element';

const Module = ({data, module, sub_module , complement_module, handleData}) => {
    
    return (
        <div>
            <h3 className="bg-update">
                <hr/>
                {sub_module}
                <hr/>
            </h3>
        {globalData.map(data, function(elt, index) {
            return (<div key={index}>
                <Element  module={module} complement_module={complement_module} sub_module={sub_module} action_id={index} data={elt} handleData={handleData} />
            </div>)
            
     })}
     </div>       
    )
}
export default Module;