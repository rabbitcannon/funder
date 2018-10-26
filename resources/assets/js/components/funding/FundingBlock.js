import React, {Component} from "react";
import CountUp from "react-countup";

class FundingBlock extends Component {
	constructor(props) {
		super(props);

		this.state = {
			playerData: sessionStorage.getItem('playerData'),
		}
	}

    render() {
		const balance = this.props.balance;
		let cashBalance = balance / 100;
		// let newBalance = parseInt(this.props.newAmount).toFixed(2) + cashBalance;
		let newBalance = this.props.newAmount / 100;
		let additionalFunds = this.props.additionalAmount;

        return (
			<div className="grid-container">

				<div className="grid-x grid-margin-x text-center fund--table">
					<div className="cell medium-4 __item">
						<div>
							<span>Current Wallet</span>
						</div>
						<div>
							<span>${cashBalance}</span>
						</div>
					</div>
					<div className="cell medium-4 __item">
						<div>
							<span>Additional Funds</span>
						</div>
						<div>
							{/*<span>{this.props.additionalAmount}</span>*/}
							<span>
								<CountUp
									end={additionalFunds}
									decimals={2}
									prefix="$"
									duration={1.5} />
							</span>
						</div>
					</div>
					<div className="cell medium-4 __item">
						<div>
							<span>New Balance</span>
						</div>
						<div>
							{/*<span>${this.props.newAmount}</span>*/}
							<span>
								<CountUp
									end={newBalance}
									decimals={2}
									prefix="$"
									duration={1.5} />
							</span>
						</div>
					</div>
				</div>

			</div>
        );
    }
}

export default FundingBlock;