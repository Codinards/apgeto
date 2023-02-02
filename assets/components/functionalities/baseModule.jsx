import React from 'react';
import { componentKeys, globalData } from '../../utils/functions';
import Module from './module';

const BaseModule = ({data, handleData, module, complement_module}) => {
console.log(data, module)
    return (
        <>
        {globalData.map(data[module], function(elt, index) {
            
            return <Module key={componentKeys.getcount()} handleData={handleData} module={module} complement_module={complement_module} sub_module={index}  data={elt} />
        })}
        </>
    )
}
export default BaseModule;