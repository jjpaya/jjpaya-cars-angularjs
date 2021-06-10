var strToNumMod = angular.module('jjcars.directive.strtonum', []);

strToNumMod.directive('stringToNum', () => {
  return {
    require: 'ngModel',
    link: (scope, element, attrs, ngModel) => {
      ngModel.$parsers.push(value => {
        return '' + value;
      });
      
      ngModel.$formatters.push(value => {
        return parseFloat(value);
      });
    }
  };
});

export default strToNumMod;
