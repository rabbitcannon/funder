import React, {Component} from "react";
import Axios from "axios";

import Address from '../layout/controls/Address';
import CreditCard from "../layout/controls/CreditCard";

import config from  "../../config/config.json";
import FundAmount from "../layout/controls/FundAmount";
import FundingBlock from "./FundingBlock";

const API_KEY = btoa(config.keys.paysafe);
let instance = null;

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

class OneTimeFunding extends Component {
	constructor(props) {
		super(props);

		this.state = {
			additionalAmount: 0,
			newAmount: 0,
			playerData: sessionStorage.getItem('playerData')
		}
	}

	componentDidMount = async () => {
		$(document).foundation();

		await window.paysafe.fields.setup(API_KEY, OPTIONS, function(paysafeInstance, error) {
			if(error) {
				console.log("Setup error: " + error.code + " " + error.detailedMessage);
			}
			else {
				instance = paysafeInstance;
			}
		});
	}

	handleAmountChange = (event) => {
		let currency = parseInt(event.target.value).toFixed(2);
		let newBalance = currency + this.state.newAmount;
		let newBalanceFormatted = parseFloat(newBalance).toFixed(2);

		this.setState({
			additionalAmount: currency, newAmount: newBalanceFormatted
		});
	}

	handlePayment = (event) => {
		let $errorSpan = $("#form-submit-error");
		$errorSpan.text("");

		event.preventDefault();

		if(!instance) {
			console.log("No instance");
		}
		else {
			console.log("instance");
		}

		instance.tokenize(function(paysafeInstance, error, result) {
			if(error) {
				$errorSpan.text("Tokenization error: " + error.code + " " + error.detailedMessage)
				console.log("Tokenization error: " + error.code + " " + error.detailedMessage);
			}
			else {
				let data = JSON.parse(sessionStorage.getItem('playerData'));
				let amount = parseInt($('#fund-amount').val()) * 100;
				let defaultCheck = $('#save_method').is(':checked');
				let saveValue = null;

				if(defaultCheck) {
					saveValue = true;
				}
				else {
					saveValue = false;
				}

				Axios.post('/api/funds/add', {
					playerHash: data.player.playerhash,
					amount: amount,
					provider_temporary_token: result.token,
					funding_method_type: "token",
					save_method: saveValue,
					billing_details: {
						address_nickname: null,
						address1: $('#address_1').val(),
						address2: $('#address_2').val(),
						city: $('#city').val(),
						state: $('#state').val(),
						country: 'US',
						zip: $('#zip').val(),
					}
				}).then(function(response) {
					console.log(response);
				}).catch(function (error) {
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
                    <h4>Add funds</h4>
                </div>

                <div className="card-section">

					<div>
						<FundingBlock additionalAmount={this.state.additionalAmount} newAmount={this.state.newAmount}/>
					</div>

                    <form id="add-funds-form" data-abide noValidate>
                        <div className="grid-container">

                            <div className="grid-x grid-margin-x">
                                <div className="cell medium-12">
                                    <div data-abide-error className="alert callout" style={styles.hidden}>
                                        <p><i className="fi-alert"></i> There are some errors in your form.</p>
                                    </div>
                                </div>
                            </div>

							<div className="grid-x grid-margin-x">
								<div className="cell medium-6">
                                    <Address/>
								</div>
								<div className="cell medium-6">
									<CreditCard/>
									<FundAmount handleAmountChange={this.handleAmountChange}/>

									<div className="grid-x grid-margin-x">
										<div className="cell medium-12">
											<input id="save_payment" type="checkbox" />
											<label htmlFor="save_payment">Save payment method?</label>
										</div>
									</div>
								</div>
							</div>

							<div className="grid-x grid-margin-x">
								<div className="cell medium-12 text-center">
									<button id="add-funds-btn" className="button"
											onClick={(event) => this.handlePayment(event)}>Add Funds</button>SCjdWt6WbgC5ObHL
								</div>
							</div>
                        </div>
                    </form>

                </div>
            </div>
        );
    }
}

export default OneTimeFunding;