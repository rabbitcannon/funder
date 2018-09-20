import React, {Component} from "react";

import Header from "./Header";
import DepositsIndex from '../deposits/Index';
import AccountIndex from '../accounts/Index';
import FundingMethods from '../funding/FundingMethods';

class Index extends Component {
	constructor(props) {
		super(props);

		this.state = {
			page: "",
			playerData: this.props.playerData || {}
		}
	}

	setPage = (page) => {
		this.setState({
			page: page
		})
		console.log(this.state.page)
	}

    render() {
		let page = this.state.page;
		let currentPage = null;

		if(page === "accounts") {
			currentPage = <AccountIndex/>;
		}
		if(page === "deposits") {
			currentPage = <DepositsIndex/>;
		}
        return (
            <div>
				<Header playerData={this.state.playerData} setPage={this.setPage.bind(this)} />
				Current page: {this.state.page}
				{/*<FundingMethods/>*/}
				<div className="grid-container">
					<div className="grid-x grid-margin-x">
						<div className="cell large-6">
							{currentPage}
						</div>
					</div>
				</div>
			</div>
        );
    }
}

export default Index;