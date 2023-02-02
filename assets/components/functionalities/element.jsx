import React from 'react';
import { componentKeys, devKeys, globalData } from '../../utils/functions';

const Element = ({module, sub_module, action_id, data, complement_module, handleData}) => {

    const originalKeys = devKeys.keys.trans

    const ajaxRequest = ( e) => {
        e.stopPropagation
        handleData( e.target.id, e.target.getAttribute('data-id'), complement_module)
    }


    return (
<div>
    <h4 className="text-purple">
        <div className="row">
            <div className="col-md-4"></div>
            <div className="col-md-8 text-capitalize">{action_id}</div>
        </div>
        <hr/>
    </h4>
   
    <ul>
    { globalData.map(data ,function(elt, index) {
        return (
        <li key={componentKeys.getcount() } className="" >
            <span className="my-flex-between">
                <span>{elt} </span>
            {(devKeys.keys.env == 'dev' && <button className={ "mb-2 li_item btn btn-" + ( module == 'ended' ? 'warning' : ( module == 'ongoing' ? 'success' : 'primary') ) } id={ module + "_" + sub_module + '_' +  action_id + '_' + index } data-id={originalKeys[module] + "_" + originalKeys[sub_module] + '_' +  originalKeys[action_id] + '_' + index } complement-module={complement_module} key={index} onClick={(e) => ajaxRequest(e)}>
               <i className={"fa fa-" + (originalKeys[module] == 'ended' ? 'arrow-down' : 'arrow-up')} data-id={originalKeys[module] + "_" + originalKeys[sub_module] + '_' +  originalKeys[action_id] + '_' + index }></i>
            </button>)}
            </span>
            <hr/>
        </li>)
    })  }
    </ul> 
</div>        
    )
}
 //{ module == 'ended' ? originalKeys['on'] : ( module == 'ongoing' ? 'close' : 'ongoing') }
export default Element;