import React, {Component} from "react";
import Axios from "axios";
import Toastr from "toastr";
import Foundation from "foundation-sites";

import Address from '../layout/controls/Address';
import CreditCard from "../layout/controls/CreditCard";
import config from  "../../config/config.json";

const API_KEY = btoa(config.keys.paysafe);
let instance = null;

Toastr.options.closeMethod = 'fadeOut';
Toastr.options.closeDuration = 300;
Toastr.options.closeEasing = 'swing';
Toastr.options.closeButton = true;
Toastr.options.preventDuplicates = true;
Toastr.options.progressBar = true;

let OPTIONS = {
	environment: "TEST",
	fields: {
		cardNumber: {
			fieldName: "#card-number",
			placeholder: "card number",
			selector: "#card-number"
		},
		expiryDate: {
			fieldName: "#exp-date",
			placeholder: "MM/YY",
			selector: "#exp-date",
		},
		cvv: {
			fieldName: "#cvv",
			placeholder: "cvv",
			selector: "#cvv",
		}
	}
};


class AddCreditCard extends Component {
	componentDidMount = async () => {
		$(document).foundation();

		await window.paysafe.fields.setup(API_KEY, OPTIONS, function(paysafeInstance, error) {
			console.log(instance);
			if(error) {
				console.log("Setup error: " + error.code + " " + error.detailedMessage);
			}
			else {
				instance = paysafeInstance;
			}
		});
	}

	handlePayment = (event) => {
		let $errorSpan = $("#form-submit-error");
		$errorSpan.text("");
		$('#add-card-btn').html('<img src="../../images/loaders/loader_pink_15.svg" /> Saving');

		event.preventDefault();

		if(!instance) {
			console.log("No instance");
		}

		instance.tokenize(function(paysafeInstance, error, result) {
			console.log(result)
			if(error) {
				$errorSpan.text("Tokenization error: " + error.code + " " + error.detailedMessage)
				console.log("Tokenization error: " + error.code + " " + error.detailedMessage);
				$('#add-card-btn').html('Add Card');
				Toastr.error(error.detailedMessage);
			}
			else {
				let data = JSON.parse(sessionStorage.getItem('playerData'));
				let defaultCheck = $('#make_default').is(':checked')
				let checkValue = null;

				if(defaultCheck) {
					checkValue = true;
				}
				else {
					checkValue = false;
				}

				Axios.post('/api/methods/add', {
					playerHash: data.player.playerhash,
					provider_temporary_token: result.token,
					payment_method_nickname: $('#account-nickname').val(),
					funding_method_type: "card_profile",
					default: checkValue,
					billing_details: {
						address_nickname: null,
						address1: $('#address_1').val(),
						address2: $('#address_2').val(),
						city: $('#city').val(),
						state: $('#state').val(),
						country: 'US',
						zip: $('#zip').val(),
					}
				}).then(function (response) {
					Toastr.success('Payment method saved.');
					console.log("---CreditCard---");
					console.log(response);
					$('form#add-card-form').trigger("reset");
					$('#add-card-btn').html('Add Card');
				}).catch(function (error) {
					Toastr.error('Error saving payment method.');
					$('#add-card-btn').html('Add Card');
					console.log(error);
				});
			}
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
											   aria-errormessage="numberError" required />
									</label>
									<span className="form-error" id="nickname-error" data-form-error-for="account-nickname">
										Please add a name to identify this account.
									</span>
								</div>
							</div>

							<div className="grid-x grid-margin-x">
								<Address />
								<CreditCard showDefault={true}/>
							</div>

							<div className="grid-x grid-margin-x">
								<div className="cell medium-12 text-center">
									<span className="form-error" id="form-submit-error"></span>
								</div>
							</div>

							<div className="grid-x grid-margin-x">
								<div className="cell medium-12 text-center">
									<button id="add-card-btn" className="button" onClick={this.handlePayment}>Add Card</button>
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