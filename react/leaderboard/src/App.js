import React from 'react';
import { BrowserRouter as Router, Switch, Route, Link } from "react-router-dom";
import './App.css';

class User extends React.Component {
  state = {
    display: false
  }
  handleUserClick = () =>
      this.setState({display:!this.state.display})
  render() {
    let info = "";
    if (this.state.display) {
      info = <p>{this.props.details.points}</p>
    }
    return (
        <div onClick={this.handleUserClick}>
          <h2>{this.props.details.username}</h2>
          <p>{this.props.details.firstname}</p>
          <p>{this.props.details.surname}</p>
          {info}
        </div>
    );
  }
}

class Search extends React.Component {
  render() {
    return (
        <div>
          <p>Search: {this.props.query}</p>
          <input
              type='text'
              placeholder='search'
              value={this.props.query}
              onChange={this.props.handleSearch}
          />
        </div>
    )
  }
}

class Users extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      page: 1,
      pageSize:9,
      query:"",
      data:  []
    }
    this.handleSearch = this.handleSearch.bind(this);
  }
  componentDidMount() {
    const url = "https://uniproj2.heliohost.us/DamiWork/Team/api/userRanking"
    fetch(url)
        .then( (response) => response.json() )
        .then( (data) => {
          console.log(data.data)
          this.setState({data:data.data})
        })
        .catch ((err) => {
              console.log("something went wrong ", err)
            }
        );
  }

  handleSearch = (e) => {
    this.setState({query:e.target.value})
  }
  searchString = (s) => {
    return s.toLowerCase().includes(this.state.query.toLowerCase())
  }

  searchDetails = (details) => {
    return ((this.searchString(details.username) || this.searchString(details.firstname)))
  }

  handleNextClick = () => {
    this.setState({page:this.state.page+1})
  }

  handlePreviousClick = () => {
    this.setState({page:this.state.page-1})
  }

  render() {
    // let button = (this.state.page>=this.state.data.length)

    let filteredData =  (
        this.state.data
            .filter(this.searchDetails)
    )

    let noOfPages = Math.ceil(filteredData.length/this.state.pageSize)
    if (noOfPages === 0) {noOfPages=1}

    let disabledPrevious = (this.state.page <= 1)
    let disabledNext = (this.state.page >= noOfPages)

    return (
        <div className={"rankingComponent"}>
          <h1>Ranking</h1>
          <Search query={this.state.query} handleSearch={this.handleSearch}/>
          {
            filteredData
                .slice((this.state.page*this.state.pageSize)-this.state.pageSize,(this.state.page*this.state.pageSize))
                .map(
                    (details, i) => (<User key={i} details={details} />))
          }
          <button onClick={this.handlePreviousClick} disabled={disabledPrevious}>Previous</button>
          Page {this.state.page} of {noOfPages}
          <button onClick={this.handleNextClick} disabled={disabledNext}>Next</button>
        </div>
    );
  }
}

function App() {
  return (
      <Router>
        <div className="App">
          <nav>
            <ul>
              <li>
                <Link to="/">Home</Link>
              </li>
              <li>
                <Link to="/leaderboard">Leaderboard</Link>
              </li>
              <li>
                <Link to="/admin">Admin</Link>
              </li>
            </ul>
          </nav>
          <Switch>
            <Route path="/leaderboard">
              <Users />
            </Route>
            <Route path="/admin">
              Admin
            </Route>
            <Route exact path="/">
              Home
            </Route>
            <Route path="*">
              404 Not Found
            </Route>
          </Switch>

        </div>
      </Router>
  );
}

export default App;