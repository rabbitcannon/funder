import React, {Component} from "react";

class CreditCard extends Component {


    render() {
        return (
			<div className="cell medium-6">
				<div className="grid-x grid-margin-x">
					<div className="cell medium-12">
						<label htmlFor="card-number">Card Number
							<input id="card-number" type="text" placeholder="card number" aria-describedby="card-number-hint"
								   aria-errormessage="card-number-error" required pattern="card" />
						</label>
						<span className="form-error" id="card-number-error" data-form-error-for="card-number">
							Credit card number required.
						</span>
						{/*<p className="help-text" id="card-number-hint">Here's how you use this input field!</p>*/}
					</div>
				</div>

				<div className="grid-x grid-margin-x">
					<div className="cell medium-2">
						<label htmlFor="expiration-date">Exp. Date
							<input id="expiration-date" type="text" placeholder="exp" aria-errormessage="exp-date-error" required pattern="number" />
						</label>
					</div>
					<div className="cell medium-2">
						<label htmlFor="cvv">CVV
							<input id="cvv" type="text" placeholder="cvv" aria-errormessage="cvv-error" required pattern="cvv" />
						</label>
					</div>
				</div>

				<div className="grid-x grid-margin-x">
					<div className="cell medium-12">
						<span className="form-error" id="exp-date-error" data-form-error-for="expiration-date">
							Expiration required.
						</span>
					</div>
				</div>
				<div className="grid-x grid-margin-x">
					<div className="cell medium-12">
						<span className="form-error" id="cvv-error" data-form-error-for="cvv">
							CVV required.
						</span>
					</div>
				</div>

			</div>
        );
    }
}

export default CreditCard;