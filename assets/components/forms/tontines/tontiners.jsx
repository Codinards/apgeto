import React, { useEffect, useState } from "react";
import eventBus from "../../../utils/eventBus";
import Tontiner from "./tontiner";

const Tontiners = ({propData}) => {

    const [tontinersData, setTontinersData] = useState({});
    const [isMounted, setIsMounted] = useState(false);
    const [data, setData] = useState({});
    const [tontineurs, setTontineurs] = useState([]);

    useEffect(() => {
        if(!isMounted){
           //const viewData = JSON.parse(document.querySelector('#react_app').getAttribute('data-tontines'))
            /*viewData.tontineurs.forEach(element => {
                
            });*/
            setData(propData.data);
            setTontinersData(propData.tontineurs);
            setIsMounted(true);
            eventBus.dispatch('changeState', {data: tontinersData})
        }
        if(isMounted) document.querySelector('#react_app').removeAttribute('data-tontines');
    }, [isMounted])

    const handleChecked = (e, index) => {
        let newTontineurs = [...tontinersData];
        //console.log(tontineurs)
        newTontineurs[index].checked = !newTontineurs[index].checked
        
        if(e.target.checked){
            const newCount = document.getElementById(`tontine_tontineurData_${index}_count`).value
            const isHalf = document.getElementById(`tontine_tontineurData_${index}_demiNom`).checked
            
            newTontineurs[index].count = parseInt(newCount, 10)
            newTontineurs[index].half = isHalf ? 1 : 0;
        }else{
            newTontineurs[index].count = 0;
            newTontineurs[index].half = 0;
        }
        eventBus.dispatch('changeState', {data: tontinersData})
        setTontinersData(newTontineurs)
        //console.log('applying')
    };

    const handleCount = (e, index) => {
        let newTontineurs = [...tontinersData];
        
        newTontineurs[index].count = e.target.value;
        setTontinersData(newTontineurs)
        
    };

    const handleHalfName = (e, index) => {
        let newTontineurs = [...tontinersData];
        if(e.target.checked){
            newTontineurs[index].half = 1;
        }else{
            newTontineurs[index].half = 0;
        }

        setTontinersData(newTontineurs)
        eventBus.dispatch('changeState', {data: tontinersData})
        
    };

    useEffect(() => {
        if(isMounted){
            let buildTontiners = [];
            for (let i = 0; i < tontinersData.length; i++) {
                buildTontiners.push(
                    <Tontiner 
                        key={i} 
                        key_prop={i} 
                        id={tontinersData[i].id} 
                        data={data} 
                        member={tontinersData[i]} 
                        handleChecked={handleChecked}
                        handleCount={handleCount}
                        handleHalfName={handleHalfName}
                    />
                );
            }
            setTontineurs(buildTontiners)
            
        }
    }, [isMounted, tontinersData])

    useEffect(() => {
        eventBus.dispatch('changeState', {data: tontinersData})
    }, [tontineurs])

    return <div>
        {tontineurs}
    </div>        
}

export default Tontiners;