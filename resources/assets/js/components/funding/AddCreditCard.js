import React, {Component} from "react";
import Foundation from 'foundation-sites';

import Address from '../layout/controls/Address';
import CreditCard from "../layout/controls/CreditCard";
import config from  "../../config/config.json";

const API_KEY = config.keys.paysafe;
let paysafeInstance = null;

let OPTIONS = {
	environment: "TEST",
	fields: {
		cardNumber: {
			selector: "#card-number",
			placeholder: "Card number"
		},
		expiryDate: {
			selector: "#expiration-date",
			placeholder: "Expiration date"
		},
		cvv: {
			selector: "#cvv",
			placeholder: "CVV"
		}
	}
};


class AddCreditCard extends Component {
	componentDidMount() {
		$(document).foundation();
console.log(API_KEY);
		// window.paysafe.fields.setup(API_KEY, OPTIONS, function(instance, error) {
		// 	console.log(paysafeInstance);
		// 	if (error) {
		// 		console.log("Setup error: " + error.code + " " + error.detailedMessage);
		// 	} else {
		// 		paysafeInstance = instance;
		// 	}
		// });
	}

	handlePayment =() => {
		$("form#add-card-form").bind("forminvalid.zf.abide", function(event, target) {
			// $(this[0]).foundation('addErrorClasses');
		});
		$("form#add-card-form").bind("formvalid.zf.abide", function(event, target) {
			window.paysafe.fields.setup(API_KEY, OPTIONS, function(instance, error) {
				console.log(paysafeInstance);
				if (error) {
					console.log("Setup error: " + error.code + " " + error.detailedMessage);
				} else {
					// paysafeInstance = instance;
					instance.tokenize(function (instance, error, result) {
						if (error) {
							alert("Tokenization error: " + error.code + " " + error.detailedMessage);
						} else {
							alert("Token: " + result.token);
						}
					});
				}
			});


			// if (!paysafeInstance) {
			// 	console.log("No instance");
			// }
			// paysafeInstance.tokenize(function (instance, error, result) {
			// 	if (error) {
			// 		alert("Tokenization error: " + error.code + " " + error.detailedMessage);
			// 	} else {
			// 		alert("Token: " + result.token);
			// 	}
			// });
		});
	}

    render() {
		const styles = {
			hidden: {
				display: 'none'
			}
		}

        return (
			<div className="card animated fadeIn">
				<div className="card-divider">
					<h4>Add a new credit card</h4>
				</div>
				<div className="card-section">

					<form id="add-card-form" data-abide noValidate>
						<div className="grid-container">

							<div className="grid-x grid-margin-x">
								<div className="cell medium-12">
									<div data-abide-error className="alert callout" style={styles.hidden}>
										<p><i className="fi-alert"></i> There are some errors in your form.</p>
									</div>
								</div>
							</div>

							<div className="grid-x grid-margin-x">
								<div className="cell medium-4">
									<label htmlFor="account-nickname">Account Nickname
										<input id="account-nickname" type="text" placeholder="account nickname"
											   aria-errormessage="numberError" required pattern="card" />
									</label>
									<span className="form-error" id="nickname-error" data-form-error-for="account-nickname">
										Please add a name to identify this account.
									</span>
								</div>
							</div>

							<div className="grid-x grid-margin-x">
								<Address />
								<CreditCard/>
							</div>

							<div className="grid-x grid-margin-x">
								<div className="cell medium-12 text-center">
									<button className="button" onClick={this.handlePayment}>Add Card</button>
								</div>
							</div>

						</div>
					</form>
				</div>
			</div>
        );
    }
}

export default AddCreditCard;