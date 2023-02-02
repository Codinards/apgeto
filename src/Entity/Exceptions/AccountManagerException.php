<?php

namespace App\Entity\Exceptions;

class AccountManagerException extends EntityException
{
    const USER_ALREADY_HAS_LOAN = 'user.already.has.loan';

    const USER_NO_HAS_LOAN_BASE_FUND = 'user.no.has.loan.base.fund';

    const NO_NEGATIVE_FUND = 'no.negative.fund';

    const FUND_NO_LESS_THAN = 'fund.no.less.than';

    const NO_EXISTING_DEBT = 'user.no.has.current.debt';

    const LOAN_OUT_FLOWS_MORE_THAN_BALANCE = 'loan.out.flows.more.than.balance';

    const LOAN_IN_FLOWS_MORE_THAN_BALANCE = 'loan.in.flows.more.than.balance';

    const PAYBACKAT_GREAT_THAN_CREATEDAT = 'payback_at.must.great.than.created_at';

    protected $params = [];



    /**
     * Get the value of params
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set the value of params
     *
     * @return  self
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Set the value of params
     *
     * @return  self
     */
    public function setParam(string $key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }
}
