import React, {Component} from "react";

import States from "./States";

class Address extends Component {
	render() {
        return (
			<div className="cell medium-6">
				<label htmlFor="card-name">Name
					<input id="card-name" type="text" placeholder="name" aria-errormessage="exp-date-error" required/>
				</label>
				<span className="form-error" id="card-name-error" data-form-error-for="card-name">
					Please enter the name for the appropriate account.
				</span>

				<label htmlFor="address_1">Address Line 1
					<input id="address_1" type="text" placeholder="address 1" required />
				</label>
				<span className="form-error" id="address-error" data-form-error-for="address_1">
					Address is required for the payment method.
				</span>

				<label htmlFor="address_2">Address Line 2
					<input id="address_2" type="text" placeholder="address 2" />
				</label>

				<div className="grid-x grid-margin-x">
					<div className="cell medium-5">
						<label htmlFor="city">City
							<input id="city" type="text" placeholder="city" required pattern="alpha" />
						</label>
						<span className="form-error" id="city-error" data-form-error-for="city">
							City required.
						</span>
					</div>

					<div className="cell medium-4">
						<States/>
						<span className="form-error" id="state-error" data-form-error-for="state">
							Select a state.
						</span>
					</div>

					<div className="cell medium-3">
						<label htmlFor="zip">Zip Code
							<input id="zip" type="text" placeholder="zip" required pattern="number" />
						</label>
						<span className="form-error" id="zip-error" data-form-error-for="zip">
							Please enter zip.
						</span>
					</div>
				</div>
			</div>
        );
    }
}

export default Address;