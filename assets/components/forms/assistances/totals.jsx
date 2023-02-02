import React, { useEffect, useState } from "react";

        const Totals = (props) => {
            console.log(props)
        const [contributorLabel, setContributorLabel] = useState('')
        const [balance, setBalance] = useState(0)
        const [totalContributorLabel, setTotalContributorLabel] = useState('')
        const [contributorsCount, setContributorsCount] = useState(0)
        const [memberLabel, setMemberLabel] = useState('')
        const [devise, setDevise] = useState('')

            useEffect(() => {
                setContributorLabel(props.contributorLabel)
                setBalance(props.balance)
                setTotalContributorLabel(props.totalContributorLabel)
                setContributorsCount(props.contributorsCount)
                setMemberLabel(props.memberLabel)
                setDevise(props.devise)
            })
            return (
                        <div className="col-12">
                            <table style={{ fontSize : '1.5rem', fontFamily : ['Georgia', 'Verdana', 'Times New Roman', 'Courier New', 'Courier', 'monospace'] }} className="table">
                                <tbody>
                                    <tr>
                                        <td className="bg-save p-2 topLeft" >
                                            <span>{contributorLabel} :</span>
                                        </td>
                                        <td className="bg-edit p-2 topRight" > 
                                            <span> {balance} {devise}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td className="bg-save p-2 botRight">
                                            <span>{totalContributorLabel} : </span>
                                        </td>
                                        <td className="bg-edit p-2  botLeft">
                                            <span id="assistance_total_contributors">{contributorsCount} {memberLabel} </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
            );
    }
export default Totals;


