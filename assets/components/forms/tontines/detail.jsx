import React, { useEffect, useState } from 'react';
import eventBus from '../../../utils/eventBus';

const Detail = ({labels, propData}) => {

    const [tontiners, setTontiners] = useState([]);
    const [view, setView] = useState([])
    const [isMounted, setIsMounted] = useState(false)
    const [unityTotalCount, setUnityTotalCount] = useState(0)
   const [amount, setAmount] = useState(0);

    useEffect(() => {
        if(!isMounted){
            setTontiners([...propData.tontineurs])
            setIsMounted(true)
        }else{
            eventBus.on('changeState', (data) => {
                setTontiners([...data.data])
            })
        }
        
        return eventBus.remove('changeState');
    })

    const calculCount = (member) => {
        const entire = parseInt(member.count, 10); // + parseInt(member.oldCount, 10)
        let half = 0;
        
        /*if(member.half === 1 && member.oldHalf === 1){
            entire += 1;
        }*/
        if(member.half === 1 /*|| member.oldHalf === 1*/){
            half = 1;
        }
       
        if(entire !== 0) return entire + ' '+ labels.unity + (entire > 1 ? 's' : '') + (half ? ' ' + labels.and_half_label : '');
        else if(half) return labels.half_label;
        return '';
    }

    useEffect(() => {
        const builtView = [];
        let totalUnity = 0;
        for (let i = 0; i < tontiners.length; i++) {
            const tontiner = tontiners[i];
            if(tontiner.checked || tontiner.oldChecked){
                //if(tontiner.count > 0 || tontiner.oldCount > 0 || tontiner.half > 0 || tontiner.oldHalf > 0){
                if(tontiner.count > 0 || tontiner.half > 0 ){
                    totalUnity += parseInt(tontiner.count, 10)// + parseInt(tontiner.oldCount, 10)
                    //totalUnity += (tontiner.oldHalf== 1 && tontiner.half == 1) ? 1 : ((tontiner.oldHalf== 1 || tontiner.half == 1) ? 0.5 : 0);
                    totalUnity +=  tontiner.half == 1 ? 0.5 : 0;
                    builtView.push(
                        <tr key={tontiner.id}>
                            <td>{tontiner.name}</td>
                            <td>{calculCount(tontiner)}</td>
                        </tr>
                    );
                }
                else{
                    builtView.push(
                        <tr key={tontiner.id}>
                            <td>{tontiner.name}</td>
                            <td>{calculCount(tontiner)}</td>
                        </tr>
                    );
                }
            }
        }
      
        document.querySelector('#member_numbers').innerHTML = view.length
        document.querySelector('#count_numbers').innerHTML = totalUnity
        document.querySelector("#tontine_total_amount").innerHTML = totalUnity * amount
        
        setView(builtView);
        
    }, [tontiners, isMounted])

    useEffect(() => {
        
    }, [view])

    return (
        <>{view}</>
    )

}

export default Detail;