import React, {Component} from "react";

class FundAmount extends Component {

    render() {
        return (
            <div>
				<label htmlFor="fund-amount">Funding Amount
					<input id="fund-amount" type="number" placeholder="amount" onChange={this.props.handleAmountChange}
						   aria-errormessage="fund-error" required/>
				</label>
				<span className="form-error" id="fund-error" data-form-error-for="fund-amount">
					Please enter an amount to charge.
				</span>
			</div>
        );
    }
}

export default FundAmount;