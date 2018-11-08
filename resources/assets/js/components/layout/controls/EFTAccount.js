import React, {Component} from "react";

class EFTAccount extends Component {

    render() {
        return (
			<div className="cell medium-6">
				<div className="grid-x grid-margin-x">
					<div className="cell medium-12">
						<label htmlFor="bank-name">Bank Name
							<input id="bank-name" type="text" placeholder="bank name" aria-describedby="bank-name-hint"
								 aria-errormessage="bank-name-error" required />
						</label>
						<span className="form-error" id="bank-name-error" data-form-error-for="bank-name">
							Please enter your bank's name.
						</span>
					</div>

					<div className="cell medium-6">
						<label htmlFor="acct-number">Account Number
							<input id="acct-number" type="text" placeholder="account number" aria-describedby="acct-number-hint"
								 aria-errormessage="acct-number-error" required pattern="number" />
						</label>
						<span className="form-error" id="acct-number-error" data-form-error-for="acct-number">
							Please enter your account number.
						</span>
					</div>

					<div className="cell medium-6">
						<label htmlFor="confirm-acct-number">Confirm Account Number
							<input id="confirm-acct-number" type="text" placeholder="confirm account" aria-describedby="confirm-acct-number-hint"
								   aria-errormessage="confirm-acct-number-error" required pattern="number"  data-equalto="acct-number" />
						</label>
						<span className="form-error" id="confirm-acct-number-error" data-form-error-for="confirm-acct-number">
							Your account numbers do not match.
						</span>
					</div>

					<div className="cell medium-6">
						<label htmlFor="routing-number">Routing Number
							<input id="routing-number" type="text" placeholder="account number" aria-describedby="routing-number-hint"
								   aria-errormessage="routing-number-error" required pattern="number" />
						</label>
						<span className="form-error" id="routing-number-error" data-form-error-for="routing-number">
							Please enter your routing number.
						</span>
					</div>

					<div className="cell medium-6">
						<label htmlFor="confirm-routing-number">Confirm Routing Number
							<input id="confirm-routing-number" type="text" placeholder="confirm routing" aria-describedby="confirm-routing-number-hint"
								   aria-errormessage="confirm-routing-number-error" required pattern="number" data-equalto="routing-number" />
						</label>
						<span className="form-error" id="confirm-routing-number-error" data-form-error-for="confirm-routing-number">
							Your routing numbers do not match.
						</span>
					</div>
				</div>
            </div>
        );
    }
}

export default EFTAccount;