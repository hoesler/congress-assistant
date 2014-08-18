define(function() {
	if (!Number.prototype.pad) {
		Number.prototype.pad = function(padding) {
			padding = padding || 0;
			min_length = (this + "").length;
			return (1e15 + this + "").slice(-Math.max(min_length, padding))
		}
	}

	return function () {};
});