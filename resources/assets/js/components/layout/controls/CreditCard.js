import React, {Component} from "react";

class CreditCard extends Component {
    render() {
    	console.log(this.props.showDefault);

        return (
			<div className="cell medium-6">
				<div className="grid-x grid-margin-x">
					<div className="cell medium-12">
						<label htmlFor="card-number">Card Number
							<div id="card-number" type="text" placeholder="card number" aria-describedby="card-number-hint"
								   aria-errormessage="card-number-error" required pattern="card" />
						</label>
						<span className="form-error" id="card-number-error" data-form-error-for="card-number">
							Credit card number required.
						</span>
					</div>
				</div>

				<div className="grid-x grid-margin-x">
					<div className="cell medium-3">
						<label htmlFor="exp-date">Exp. Date
							<div id="exp-date" type="text" placeholder="exp" aria-errormessage="exp-date-error" required />
						</label>
					</div>
					<div className="cell medium-2">
						<label htmlFor="cvv">CVV
							<div id="cvv" type="text" placeholder="cvv" aria-errormessage="cvv-error" required pattern="cvv" />
						</label>
					</div>
				</div>

				<div className="grid-x grid-margin-x">
					<div className="cell medium-12">
						<span className="form-error" id="exp-date-error" data-form-error-for="exp-date">
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

				<div className="grid-x grid-margin-x" style={{ visibility: this.props.showDefault == true ? 'visible': 'hidden'}}>
					<div className="cell medium-12">
						<input id="make_default" type="checkbox" />
						<label htmlFor="make_default">Make default</label>
					</div>
				</div>
			</div>
        );
    }
}

export default CreditCard;